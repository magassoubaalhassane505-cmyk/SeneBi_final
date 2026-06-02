<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\User;

echo "Users in database: " . User::count() . "\n";
foreach (User::all() as $user) {
    echo $user->email . ' - ' . $user->role . " (Active: " . ($user->is_active ? 'yes' : 'no') . ")\n";
}
