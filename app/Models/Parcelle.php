<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nom', 'region', 'surface', 'culture', 'user_id', 'latitude', 'longitude', 'last_irrigation', 'last_traitement', 'next_intervention'])]
class Parcelle extends Model
{
    use HasFactory;

    protected $casts = [
        'surface' => 'decimal:2',
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
}
