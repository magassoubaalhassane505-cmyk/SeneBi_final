<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class NormalizeUserEmails extends Command
{
    protected $signature = 'senebi:normalize-emails';

    protected $description = 'Normalise les emails utilisateurs (trim + minuscules)';

    public function handle(): int
    {
        $updated = 0;

        foreach (User::all() as $user) {
            $normalized = Str::lower(trim($user->email));

            if ($normalized !== $user->email) {
                if (User::where('email', $normalized)->where('id', '!=', $user->id)->exists()) {
                    $this->warn("Doublon ignoré : {$user->email} -> {$normalized}");
                    continue;
                }

                $user->update(['email' => $normalized]);
                $updated++;
            }
        }

        $this->info("{$updated} email(s) normalisé(s).");

        return self::SUCCESS;
    }
}
