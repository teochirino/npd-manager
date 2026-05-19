<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Actualizando SOLO la contraseña de rbarraza@lineaitalia.com...\n\n";

$user = User::where('email', 'rbarraza@lineaitalia.com')->first();

if (!$user) {
    echo "❌ ERROR: Usuario no encontrado en la base de datos\n";
    exit(1);
}

echo "Usuario encontrado:\n";
echo "- Nombre: {$user->name}\n";
echo "- Email: {$user->email}\n";
echo "- Perfil: {$user->perfil}\n";
echo "- Role: {$user->role}\n\n";

// Actualizar SOLO el password
$user->password = Hash::make('rb2026');
$user->save();

echo "✅ Password actualizado a 'rb2026'\n\n";

// Verificar
if (Hash::check('rb2026', $user->password)) {
    echo "✅ VERIFICACIÓN EXITOSA: El password 'rb2026' funciona correctamente\n";
} else {
    echo "❌ ERROR: El password no se guardó correctamente\n";
}
