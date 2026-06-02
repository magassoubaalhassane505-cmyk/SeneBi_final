<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\User::where('role', 'client')->orderByDesc('id')->take(8)->get() as $u) {
    echo $u->id . '|[' . $u->email . ']|len=' . strlen($u->email) . '|active=' . json_encode($u->is_active) . '|status=' . $u->status . PHP_EOL;
}
