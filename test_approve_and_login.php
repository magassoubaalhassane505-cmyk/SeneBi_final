<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

[$email, $pass] = preg_split('/\R/', trim(file_get_contents(__DIR__ . '/last_e2e_email.txt')));
$email = trim($email);
$pass = trim($pass);

$u = User::where('email', $email)->firstOrFail();
$u->update(['is_active' => true, 'status' => 'approved']);
$u->refresh();

echo "Approved. hash=" . (Hash::check($pass, $u->password) ? 'OK' : 'FAIL') . PHP_EOL;
