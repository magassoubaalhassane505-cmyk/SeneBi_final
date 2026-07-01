<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$managers = \App\Models\User::where('role', 'manager')->get(['id', 'name', 'email', 'role']);

echo "Managers dans la base:\n";
foreach ($managers as $m) {
    echo "- ID: {$m->id}, Nom: {$m->name}, Email: {$m->email}, Role: {$m->role}\n";
}
echo "\nTotal: {$managers->count()}\n";
