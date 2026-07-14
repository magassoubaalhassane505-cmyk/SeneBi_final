<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nom', 'region', 'surface', 'culture', 'user_id', 'planting_date', 'status', 'latitude', 'longitude', 'last_irrigation', 'last_traitement', 'next_intervention'])]
class Parcelle extends Model
{
    use HasFactory;

    protected $casts = [
        'surface' => 'decimal:2',
        'planting_date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recoltes()
    {
        return $this->hasMany(Recolte::class);
    }

    public function visites()
    {
        return $this->hasMany(Visite::class);
    }

    public function intrantsConsommes()
    {
        return $this->hasMany(IntrantConsomme::class);
    }

    public function photos()
    {
        return $this->hasMany(ParcellePhoto::class);
    }

    public function getHealthScoreAttribute()
    {
        $score = 0;

        $recoltesCount = $this->recoltes()->count();
        $visitesCount = $this->visites()->count();
        $intrantsCount = $this->intrantsConsommes()->count();

        $score += $recoltesCount >= 3 ? 30 : ($recoltesCount >= 1 ? 15 : 5);

        if ($this->last_irrigation) {
            $daysSinceIrrigation = now()->diffInDays($this->last_irrigation);
            $score += $daysSinceIrrigation < 7 ? 20 : ($daysSinceIrrigation < 14 ? 10 : 5);
        }

        $score += $visitesCount >= 2 ? 25 : ($visitesCount >= 1 ? 15 : 10);
        $score += $intrantsCount >= 5 ? 25 : ($intrantsCount >= 2 ? 15 : 10);

        return min(100, max(0, $score));
    }

    public function getHealthLabelAttribute()
    {
        $score = $this->health_score;
        if ($score >= 80) return 'Excellent';
        if ($score >= 50) return 'Bon';
        if ($score >= 30) return 'Moyen';
        return 'Critique';
    }

    public function getCultureDurationAttribute()
    {
        $firstRecolte = $this->recoltes()->oldest()->first();
        if (!$firstRecolte) return 0;
        return now()->diffInDays($firstRecolte->date_recolte);
    }

    public function getProductionEstimeeAttribute()
    {
        $avgRendement = $this->recoltes()->avg('quantite') ?? 0;
        return $avgRendement * (float) $this->surface;
    }

    public function getDateRecolteEstimeeAttribute()
    {
        $lastRecolte = $this->recoltes()->latest()->first();
        if (!$lastRecolte) return null;
        return now()->addDays(90)->format('d/m/Y');
    }

    /**
     * Cout reel investi sur la parcelle : somme des depenses en intrants
     * (quantite consommee x cout unitaire du stock) + couts enregistres des recoltes.
     */
    public function getCostAttribute()
    {
        $intrants = $this->intrantsConsommes()
            ->with('stock')
            ->get()
            ->sum(function ($i) {
                return $i->quantite_consommee * ($i->stock?->cout_unitaire ?? 0);
            });

        $recoltes = $this->recoltes()->sum('couts_totaux');

        return (float) $intrants + (float) $recoltes;
    }

    /**
     * Pourcentage de croissance estime a partir de la date de semis
     * (cycle de culture estime a 120 jours).
     */
    public function getGrowthAttribute()
    {
        if (!$this->planting_date) return 0;
        $cycleDays = 120;
        $days = abs(now()->diffInDays($this->planting_date));
        return min(100, (int) round(($days / $cycleDays) * 100));
    }

    /**
     * Journal d'activites reel, trie chronologiquement (semis, intrants,
     * visites, traitements, recoltes).
     */
    public function getJournalAttribute()
    {
        $entries = [];

        $plantingDateFr = null;
        if ($this->planting_date) {
            $plantingDateFr = $this->planting_date instanceof \DateTime
                ? $this->planting_date->format('d/m/Y')
                : date('d/m/Y', strtotime($this->planting_date));
        }

        if ($plantingDateFr) {
            $entries[] = [
                'type' => 'semis',
                'date' => $this->planting_date,
                'label' => 'Semis',
                'value' => 'Culture semee le ' . $plantingDateFr,
            ];
        }

        foreach ($this->intrantsConsommes()->with('stock')->get() as $i) {
            $entries[] = [
                'type' => 'intrant',
                'date' => $i->date_consommation,
                'label' => 'Intrant applique',
                'value' => ($i->stock?->nom ?? 'Intrant') . ' : ' . number_format($i->quantite_consommee, 0, ',', ' ') . ' kg',
            ];
        }

        foreach ($this->visites as $v) {
            $desc = $v->action_effectuee ?? 'Visite';
            if (!empty($v->recommandation)) {
                $desc .= ' — ' . $v->recommandation;
            }
            $entries[] = [
                'type' => 'visite',
                'date' => $v->date_visite,
                'label' => 'Visite',
                'value' => $desc,
            ];
        }

        if ($this->last_traitement) {
            $entries[] = [
                'type' => 'traitement',
                'date' => $this->last_traitement,
                'label' => 'Traitement',
                'value' => 'Dernier traitement le ' . $this->last_traitement->format('d/m/Y'),
            ];
        }

        foreach ($this->recoltes as $r) {
            $entries[] = [
                'type' => 'recolte',
                'date' => $r->date_recolte,
                'label' => 'Recolte',
                'value' => number_format($r->quantite, 0, ',', ' ') . ' kg recoltes',
            ];
        }

        usort($entries, function ($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });

        return array_slice($entries, -10);
    }
}
