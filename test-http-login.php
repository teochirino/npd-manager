<?php

echo "Probando login via HTTP (simulando frontend):\n\n";

$credentials = [
    ['email' => 'gerente@lineaitalia.com', 'password' => 'gerente2026'],
    ['email' => 'rbarraza@lineaitalia.com', 'password' => 'rb2026'],
];

foreach ($credentials as $cred) {
    echo "Probando: {$cred['email']} / {$cred['password']}\n";
    
    $ch = curl_init('http://127.0.0.1:8000/api/login');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cred));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    
    if ($httpCode === 200) {
        echo "✅ Login EXITOSO\n";
        $data = json_decode($response, true);
        echo "Usuario: {$data['user']['name']}\n";
    } else {
        echo "❌ Login FALLIDO\n";
        echo "Respuesta: $response\n";
    }
    
    echo "\n";
}
