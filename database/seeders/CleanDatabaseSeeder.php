<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CleanDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Supprimer toutes les données de test
        DB::table('notifications')->delete();
        DB::table('objectifs')->delete();
        DB::table('visites')->delete();
        DB::table('intrant_consommes')->delete();
        DB::table('stock_mouvements')->delete();
        DB::table('stocks')->delete();
        DB::table('recoltes')->delete();
        DB::table('parcelles')->delete();
        DB::table('pdf_exports')->delete();

        // Supprimer les utilisateurs de démo (sauf admin/manager)
        $demoEmails = [
            'test@example.com',
            'agriculteur0@test.com',
            'agriculteur1@test.com',
            'agriculteur2@test.com',
            'agriculteur3@test.com',
            'agriculteur4@test.com',
            'agriculteur5@test.com',
        ];

        User::whereIn('email', $demoEmails)->whereNotIn('role', ['admin', 'manager'])->delete();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info('Base de données nettoyée avec succès.');
        $this->command->info('- Toutes les données de test ont été supprimées.');
        $this->command->info('- Les comptes admin/manager ont été préservés.');
    }
}
