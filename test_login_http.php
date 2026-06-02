<?php

[$email, $pass] = preg_split('/\R/', trim(file_get_contents(__DIR__ . '/last_e2e_email.txt')));
$email = trim($email);
$pass = trim($pass);

$ch = curl_init('http://127.0.0.1:8000/login');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true]);
$resp = curl_exec($ch);
preg_match('/XSRF-TOKEN=([^;]+)/', $resp, $m);
preg_match('/laravel_session=([^;]+)/', $resp, $s);
preg_match('/name="_token" value="([^"]+)"/', $resp, $t);

$ch2 = curl_init('http://127.0.0.1:8000/login');
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $t[1],
        'email' => $email,
        'password' => $pass,
    ]),
    CURLOPT_HTTPHEADER => [
        'Cookie: XSRF-TOKEN=' . urldecode($m[1] ?? '') . '; laravel_session=' . urldecode($s[1] ?? ''),
    ],
]);
$resp2 = curl_exec($ch2);
$code = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
preg_match('/Location: ([^\r\n]+)/', $resp2, $loc);

echo "HTTP $code\n";
echo "Location: " . ($loc[1] ?? 'none') . "\n";
echo "bad: " . (str_contains($resp2, 'Identifiants incorrects') ? 'yes' : 'no') . "\n";
echo "inactive: " . (str_contains($resp2, 'pas encore') ? 'yes' : 'no') . "\n";
