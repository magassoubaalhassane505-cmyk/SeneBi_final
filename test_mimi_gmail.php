<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$u = User::where('email', 'mimi@gmail.com')->first();
$hash = $u->getRawOriginal('password');
echo 'starts with $2y$: ' . (str_starts_with($hash, '$2y$') ? 'yes' : 'no') . PHP_EOL;
echo 'len: ' . strlen($hash) . PHP_EOL;
echo 'isHashed: ' . (Hash::isHashed($hash) ? 'yes' : 'no') . PHP_EOL;

// Test if double-hash scenario
$plain = 'TestPass99';
$single = Hash::make($plain);
$double = Hash::make($single);
echo 'check plain vs double: ' . (Hash::check($plain, $double) ? 'OK' : 'FAIL') . PHP_EOL;
echo 'check single vs double: ' . (Hash::check($single, $double) ? 'OK' : 'FAIL') . PHP_EOL;
