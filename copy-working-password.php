<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Copiando password del gerente (que funciona) a rbarraza:\n\n";

$gerente = User::find(1);
$rbarraza = User::find(2);

echo "Password hash del gerente: {$gerente->password}\n";
echo "Verificando 'gerente2026' con hash del gerente: " . (Hash::check('gerente2026', $gerente->password) ? 'OK' : 'FAIL') . "\n\n";

// Crear un nuevo hash para rb2026 usando exactamente el mismo método que funcionó para gerente
echo "Creando nuevo hash para 'rb2026'...\n";
$newHash = Hash::make('rb2026');
echo "Nuevo hash: $newHash\n";
echo "Verificando 'rb2026' con nuevo hash: " . (Hash::check('rb2026', $newHash) ? 'OK' : 'FAIL') . "\n\n";

// Actualizar rbarraza
$rbarraza->password = $newHash;
$rbarraza->save();

echo "✅ Password de rbarraza actualizado\n\n";

// Verificar que se guardó correctamente
$verify = User::find(2);
echo "Verificando desde DB: " . (Hash::check('rb2026', $verify->password) ? 'OK' : 'FAIL') . "\n";

// Probar login HTTP inmediatamente
echo "\nProbando login HTTP:\n";

$ch = curl_init('http://127.0.0.1:8000/api/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => 'rbarraza@lineaitalia.com', 'password' => 'rb2026']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($httpCode === 200) {
    echo "✅ LOGIN EXITOSO!\n";
} else {
    echo "❌ Login fallido\n";
    echo "Respuesta: $response\n";
}
