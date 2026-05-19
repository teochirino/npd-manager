<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Verificando usuarios en la base de datos:\n\n";

$users = User::whereIn('email', ['gerente@lineaitalia.com', 'rbarraza@lineaitalia.com'])->get();

foreach ($users as $user) {
    echo "Usuario: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Hash actual: " . substr($user->password, 0, 30) . "...\n";
    
    // Probar diferentes passwords
    $passwords = ['gerente2026', 'rb2026', 'password', '123456'];
    
    foreach ($passwords as $pwd) {
        if (Hash::check($pwd, $user->password)) {
            echo "✅ Password CORRECTO: '{$pwd}'\n";
            break;
        }
    }
    
    echo "\n";
}
