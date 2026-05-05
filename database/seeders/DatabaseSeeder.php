<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::unguard();
        User::create([
            'name' => 'Mimi Manager',
            'email' => 'mimi.manager@senebi.ml',
            'password' => bcrypt('manager123'),
            'role' => 'admin',
            'saison' => '2024',
        ]);

        User::create([
            'name' => 'Client Test',
            'email' => 'sidi@sidi-agri.ml',
            'password' => bcrypt('client123'),
            'role' => 'client',
            'saison' => '2024',
        ]);
        User::reguard();
    }
}
