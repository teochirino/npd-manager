<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Ruta named 'login' para el middleware auth (redirige aquí si no está autenticado)
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

// Ruta principal (también muestra el Login)
Route::get('/', function () {
    return Inertia::render('Login');
});

// Dashboard (acceso libre - la protección se hace en el frontend con tokens)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
});

// Ruta de logout (opcional, también se puede hacer desde el frontend)
Route::post('/logout', function () {
    return redirect('/');
})->name('logout');