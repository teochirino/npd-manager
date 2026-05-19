<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Simulando login exactamente como lo hace AuthController:\n\n";

$testCases = [
    ['email' => 'gerente@lineaitalia.com', 'password' => 'gerente2026'],
    ['email' => 'rbarraza@lineaitalia.com', 'password' => 'rb2026'],
];

foreach ($testCases as $credentials) {
    echo "Probando: {$credentials['email']} / {$credentials['password']}\n";
    
    $user = User::where('email', $credentials['email'])->first();
    
    if (!$user) {
        echo "❌ Usuario NO encontrado\n\n";
        continue;
    }
    
    echo "✅ Usuario encontrado: {$user->name}\n";
    echo "   ID: {$user->id}\n";
    echo "   Email: {$user->email}\n";
    echo "   Perfil: {$user->perfil}\n";
    
    $passwordCheck = Hash::check($credentials['password'], $user->password);
    
    if ($passwordCheck) {
        echo "✅ Password CORRECTO - Login exitoso\n";
        
        // Intentar crear token
        try {
            $token = $user->createToken('test-token')->plainTextToken;
            echo "✅ Token creado exitosamente\n";
        } catch (\Exception $e) {
            echo "❌ Error creando token: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Password INCORRECTO - Login fallido\n";
    }
    
    echo "\n";
}
