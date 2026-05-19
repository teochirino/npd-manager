<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Buscando usuario rbarraza@lineaitalia.com...\n";

$user = User::where('email', 'rbarraza@lineaitalia.com')->first();

if ($user) {
    echo "✅ Usuario encontrado: {$user->name}\n";
    echo "Actualizando password a 'rb2026'...\n";
    $user->password = Hash::make('rb2026');
    $user->save();
    echo "✅ Password actualizado exitosamente\n";
} else {
    echo "❌ Usuario NO encontrado. Creándolo...\n";
    $user = User::create([
        'name' => 'Ramón Barraza',
        'email' => 'rbarraza@lineaitalia.com',
        'password' => Hash::make('rb2026'),
        'perfil' => 'RB',
        'role' => 'Ingeniero de Producto',
        'initials' => 'RB',
    ]);
    echo "✅ Usuario creado exitosamente\n";
}

echo "\nVerificando password...\n";
if (Hash::check('rb2026', $user->password)) {
    echo "✅ Password 'rb2026' es CORRECTO\n";
} else {
    echo "❌ Password 'rb2026' es INCORRECTO\n";
}

echo "\nListando todos los usuarios:\n";
$users = User::all(['email', 'name']);
foreach ($users as $u) {
    echo "- {$u->email} ({$u->name})\n";
}
