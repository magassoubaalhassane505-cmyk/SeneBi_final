<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->date('planting_date')->nullable()->after('culture');
            $table->string('status')->default('En culture')->after('planting_date');
        });

        // Backfill : plantation = date de la premiere activite enregistree
        // (premier intrant consomme, sinon premiere visite, sinon premiere recolte)
        $parcelles = DB::table('parcelles')->get();
        foreach ($parcelles as $parcelle) {
            $dates = [];

            $intrant = DB::table('intrant_consommes')
                ->where('parcelle_id', $parcelle->id)
                ->orderBy('date_consommation')
                ->value('date_consommation');
            if ($intrant) {
                $dates[] = $intrant;
            }

            $visite = DB::table('visites')
                ->where('parcelle_id', $parcelle->id)
                ->orderBy('date_visite')
                ->value('date_visite');
            if ($visite) {
                $dates[] = $visite;
            }

            $recolte = DB::table('recoltes')
                ->where('parcelle_id', $parcelle->id)
                ->orderBy('date_recolte')
                ->value('date_recolte');
            if ($recolte) {
                $dates[] = $recolte;
            }

            $plantingDate = $dates ? min($dates) : null;

            DB::table('parcelles')
                ->where('id', $parcelle->id)
                ->update([
                    'planting_date' => $plantingDate,
                    'status' => 'En culture',
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->dropColumn(['planting_date', 'status']);
        });
    }
};
