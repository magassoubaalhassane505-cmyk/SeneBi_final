<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$plain = 'MonMotDePasse99';
$testEmail = 'test-register-bug@example.com';

User::where('email', $testEmail)->delete();

// Ancien flux RegisterController
User::create([
    'name' => 'Test',
    'email' => $testEmail,
    'password' => Hash::make($plain),
    'role' => 'client',
    'status' => 'approved',
    'is_active' => true,
]);

$u = User::where('email', $testEmail)->first();
echo "OLD RegisterController flow: " . (Hash::check($plain, $u->password) ? 'OK' : 'FAIL') . PHP_EOL;

User::where('email', $testEmail)->delete();

// Flux corrigé
User::create([
    'name' => 'Test',
    'email' => $testEmail,
    'password' => $plain,
    'role' => 'client',
    'status' => 'approved',
    'is_active' => true,
]);

$u = User::where('email', $testEmail)->first();
echo "FIXED flow (plain + cast): " . (Hash::check($plain, $u->password) ? 'OK' : 'FAIL') . PHP_EOL;

User::where('email', $testEmail)->delete();

// Clients réels approuvés récents
foreach (User::whereIn('id', [9, 10, 11])->get() as $client) {
    echo "User {$client->id} {$client->email}: cannot verify without knowing password\n";
}
