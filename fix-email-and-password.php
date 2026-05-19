<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Limpiando y actualizando usuario rbarraza:\n\n";

// Buscar por ID en lugar de email por si el email tiene problemas
$rbarraza = User::find(2);

if (!$rbarraza) {
    echo "❌ Usuario con ID 2 no encontrado\n";
    exit(1);
}

echo "Usuario encontrado:\n";
echo "ID: {$rbarraza->id}\n";
echo "Email actual: '{$rbarraza->email}'\n";
echo "Email length: " . strlen($rbarraza->email) . "\n";
echo "Email hex: " . bin2hex($rbarraza->email) . "\n";

// Limpiar y actualizar email y password
$cleanEmail = trim('rbarraza@lineaitalia.com');
$newPassword = Hash::make('rb2026');

echo "\nActualizando:\n";
echo "Nuevo email: '$cleanEmail'\n";
echo "Nuevo email hex: " . bin2hex($cleanEmail) . "\n";

$rbarraza->email = $cleanEmail;
$rbarraza->password = $newPassword;
$rbarraza->save();

echo "\n✅ Usuario actualizado exitosamente\n";

// Verificar
$verify = User::where('email', 'rbarraza@lineaitalia.com')->first();
if ($verify && Hash::check('rb2026', $verify->password)) {
    echo "✅ Verificación exitosa: Email y password correctos\n";
} else {
    echo "❌ Verificación fallida\n";
}
