<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

$email = 'flow-test-' . time() . '@senebi.test';
$plain = 'MonSecret99!';

$user = User::create([
    'name' => 'Flow Test',
    'email' => $email,
    'phone' => '+22300000000',
    'company' => 'Test Co',
    'location' => 'Bamako',
    'password' => Hash::make($plain), // comme RegisterController actuel
    'role' => 'client',
    'status' => 'pending',
    'is_active' => false,
]);

echo "Created id={$user->id}\n";
echo "Hash check after create: " . (Hash::check($plain, $user->fresh()->password) ? 'OK' : 'FAIL') . "\n";

$user->update(['is_active' => true, 'status' => 'approved']);
$user->refresh();

echo "After approve is_active={$user->is_active} status={$user->status}\n";
echo "Hash check after approve: " . (Hash::check($plain, $user->password) ? 'OK' : 'FAIL') . "\n";

$found = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
echo "Found user: " . ($found ? 'yes' : 'no') . "\n";
echo "Auth attempt: " . (Auth::attempt(['email' => $email, 'password' => $plain]) ? 'OK' : 'FAIL') . "\n";

$user->delete();
