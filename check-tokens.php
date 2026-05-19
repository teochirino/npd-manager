<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Verificando tokens de usuarios:\n\n";

$users = User::whereIn('id', [1, 2])->get();

foreach ($users as $user) {
    echo "Usuario: {$user->name} ({$user->email})\n";
    echo "Tokens activos: " . $user->tokens()->count() . "\n";
    
    if ($user->tokens()->count() > 0) {
        echo "Eliminando tokens antiguos...\n";
        $user->tokens()->delete();
        echo "✅ Tokens eliminados\n";
    }
    
    echo "\n";
}
