<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Comparando hashes de password:\n\n";

$gerente = User::where('email', 'gerente@lineaitalia.com')->first();
$rbarraza = User::where('email', 'rbarraza@lineaitalia.com')->first();

echo "GERENTE:\n";
echo "Email: '{$gerente->email}'\n";
echo "Email bytes: " . bin2hex($gerente->email) . "\n";
echo "Password hash: {$gerente->password}\n";
echo "Hash length: " . strlen($gerente->password) . "\n";
echo "Test 'gerente2026': " . (Hash::check('gerente2026', $gerente->password) ? 'OK' : 'FAIL') . "\n";

echo "\nRBARRAZA:\n";
echo "Email: '{$rbarraza->email}'\n";
echo "Email bytes: " . bin2hex($rbarraza->email) . "\n";
echo "Password hash: {$rbarraza->password}\n";
echo "Hash length: " . strlen($rbarraza->password) . "\n";
echo "Test 'rb2026': " . (Hash::check('rb2026', $rbarraza->password) ? 'OK' : 'FAIL') . "\n";

echo "\nCreando nuevo hash para 'rb2026':\n";
$newHash = Hash::make('rb2026');
echo "Nuevo hash: $newHash\n";
echo "Test con nuevo hash: " . (Hash::check('rb2026', $newHash) ? 'OK' : 'FAIL') . "\n";

echo "\nActualizando usuario con nuevo hash...\n";
$rbarraza->password = $newHash;
$rbarraza->save();
echo "✅ Usuario actualizado\n";
