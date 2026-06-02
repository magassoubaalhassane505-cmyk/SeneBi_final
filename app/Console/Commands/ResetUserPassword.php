<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ResetUserPassword extends Command
{
    protected $signature = 'senebi:reset-user-password {email} {password}';

    protected $description = 'Réinitialise le mot de passe d\'un utilisateur (support / comptes bloqués)';

    public function handle(): int
    {
        $email = Str::lower(trim($this->argument('email')));
        $user = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();

        if (! $user) {
            $this->error("Utilisateur introuvable : {$email}");

            return self::FAILURE;
        }

        $user->password = $this->argument('password');
        $user->save();

        $this->info("Mot de passe mis à jour pour {$user->email} ({$user->name}).");

        return self::SUCCESS;
    }
}
