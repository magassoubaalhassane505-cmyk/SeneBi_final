<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'aminatas@gmail.com'],
            [
                'name' => 'Aminata Manager',
                'password' => bcrypt('manager123'),
                'role' => 'manager',
                'is_active' => true,
            ]
        );

        $this->command->info('Compte manager créé : aminatas@gmail.com');
    }
}
