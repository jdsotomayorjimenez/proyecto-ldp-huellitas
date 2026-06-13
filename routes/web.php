<?php

use App\Http\Controllers\Admin\AdoptanteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImagenMascotaController;
use App\Http\Controllers\Admin\MascotaController;
use App\Http\Controllers\Admin\RazaController;
use App\Http\Controllers\Admin\RequisitoAdopcionController;
use App\Http\Controllers\Admin\TipoMascotaController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
});

Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/adoptantes/crear', [AdoptanteController::class, 'create'])
            ->name('adoptantes.create');
        Route::post('/adoptantes', [AdoptanteController::class, 'store'])
            ->name('adoptantes.store');
        Route::resource('tipos-mascotas', TipoMascotaController::class)
            ->parameters(['tipos-mascotas' => 'tipo'])
            ->except('show');
        Route::resource('razas', RazaController::class)->except('show');
        Route::resource('mascotas', MascotaController::class)->except('show');
        Route::resource('requisitos', RequisitoAdopcionController::class)->except('show');

        Route::post(
            'mascotas/{mascota}/imagenes',
            [ImagenMascotaController::class, 'store'],
        )->name('mascotas.imagenes.store');

        Route::delete(
            'imagenes/{imagenMascota}',
            [ImagenMascotaController::class, 'destroy'],
        )->name('imagenes.destroy');
    });
