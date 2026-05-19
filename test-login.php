<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'rbarraza@lineaitalia.com')->first();

if (!$user) {
    echo "Usuario no encontrado\n";
    exit(1);
}

echo "Usuario encontrado: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Perfil: {$user->perfil}\n";
echo "Password hash: {$user->password}\n\n";

$testPassword = 'rb2026';
echo "Probando password: {$testPassword}\n";

if (Hash::check($testPassword, $user->password)) {
    echo "✅ Password CORRECTO\n";
} else {
    echo "❌ Password INCORRECTO\n";
    echo "Actualizando password...\n";
    $user->password = Hash::make($testPassword);
    $user->save();
    echo "✅ Password actualizado\n";
}
