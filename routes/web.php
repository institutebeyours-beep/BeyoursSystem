<?php

use Illuminate\Support\Facades\Route;

// Ruta principal que carga Vue
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');

// Definir rutas de autenticación para redirecciones
Route::get('/login', function () {
    return view('app');
})->name('login');

Route::get('/register', function () {
    return view('app');
})->name('register');

// ✅ CAMBIAR EL NOMBRE DE LA RUTA
Route::get('/password/reset/{token}', function () {
    return view('app');
})->name('web.password.reset');  // ← Cambiado de 'password.reset' a 'web.password.reset'