<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Comparando usuarios:\n\n";

$gerente = User::where('email', 'gerente@lineaitalia.com')->first();
$rbarraza = User::where('email', 'rbarraza@lineaitalia.com')->first();

echo "GERENTE (funciona):\n";
echo json_encode($gerente->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "\n\nRBARRAZA (no funciona):\n";
echo json_encode($rbarraza->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "\n\nDiferencias:\n";
$gerenteArray = $gerente->toArray();
$rbarrazaArray = $rbarraza->toArray();

foreach ($gerenteArray as $key => $value) {
    if ($key === 'password') continue;
    if ($gerenteArray[$key] !== $rbarrazaArray[$key]) {
        echo "- $key: gerente='{$gerenteArray[$key]}' vs rbarraza='{$rbarrazaArray[$key]}'\n";
    }
}
