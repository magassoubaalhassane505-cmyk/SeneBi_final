<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$raw = trim(file_get_contents(__DIR__ . '/last_e2e_email.txt'));
[$email, $pass] = preg_split('/\R/', $raw);
$email = trim($email);
$pass = trim($pass);

echo "Looking for: [$email]\n";
echo "DB: " . config('database.connections.mysql.database') . "\n";

$u = App\Models\User::where('email', $email)->first();
if (! $u) {
    echo "Latest users:\n";
    foreach (App\Models\User::orderByDesc('id')->take(3)->get(['id', 'email']) as $row) {
        echo "  {$row->id} [{$row->email}]\n";
    }
    exit(1);
}

echo 'hash=' . (Illuminate\Support\Facades\Hash::check($pass, $u->password) ? 'OK' : 'FAIL') . PHP_EOL;
echo 'active=' . json_encode($u->is_active) . ' status=' . $u->status . PHP_EOL;
