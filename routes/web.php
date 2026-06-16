<?php

use App\Http\Controllers\Admin\AdopcionController;
use App\Http\Controllers\Admin\AdoptanteController;
use App\Http\Controllers\Admin\CitaAdopcionController;
use App\Http\Controllers\Admin\CumplimientoRequisitoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImagenMascotaController;
use App\Http\Controllers\Admin\MascotaController;
use App\Http\Controllers\Admin\RazaController;
use App\Http\Controllers\Admin\RequisitoAdopcionController;
use App\Http\Controllers\Admin\SolicitudAdopcionController as AdminSolicitudAdopcionController;
use App\Http\Controllers\Admin\TipoMascotaController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\MascotaPublicController;
use App\Http\Controllers\SolicitudAdopcionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MascotaPublicController::class, 'home'])->name('home');
Route::get('/mascotas', [MascotaPublicController::class, 'catalogo'])->name('mascotas.catalogo');
Route::get('/mascotas/{mascota}', [MascotaPublicController::class, 'show'])->name('mascotas.show.public');

Route::middleware('auth')->group(function () {
    Route::get('/mascotas/{mascota}/solicitar', [SolicitudAdopcionController::class, 'create'])
        ->name('solicitudes.create');
    Route::post('/mascotas/{mascota}/solicitar', [SolicitudAdopcionController::class, 'store'])
        ->name('solicitudes.store');
    Route::get('/mis-solicitudes', [SolicitudAdopcionController::class, 'misSolicitudes'])
        ->name('solicitudes.mis');
    Route::post('/solicitudes/{solicitud}/cancelar', [SolicitudAdopcionController::class, 'cancelar'])
        ->name('solicitudes.cancelar');
    Route::get('/mis-adopciones/{adopcion}/certificado', [SolicitudAdopcionController::class, 'certificado'])
        ->name('adopciones.certificado');
});

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
        Route::get('requisitos/generales/{requisito}/editar', [RequisitoAdopcionController::class, 'editGeneral'])
            ->name('requisitos.editar-general');
        Route::put('requisitos/generales/{requisito}', [RequisitoAdopcionController::class, 'updateGeneral'])
            ->name('requisitos.update-general');
        Route::delete('requisitos/generales/{requisito}', [RequisitoAdopcionController::class, 'destroyGeneral'])
            ->name('requisitos.destroy-general');
        Route::resource('requisitos', RequisitoAdopcionController::class)->except('show');

        Route::post(
            'mascotas/{mascota}/imagenes',
            [ImagenMascotaController::class, 'store'],
        )->name('mascotas.imagenes.store');

        Route::delete(
            'imagenes/{imagenMascota}',
            [ImagenMascotaController::class, 'destroy'],
        )->name('imagenes.destroy');

        // Flujo de adopción
        Route::get('solicitudes', [AdminSolicitudAdopcionController::class, 'index'])
            ->name('solicitudes.index');
        Route::get('solicitudes/{solicitud}', [AdminSolicitudAdopcionController::class, 'show'])
            ->name('solicitudes.show');
        Route::post('solicitudes/{solicitud}/responder', [AdminSolicitudAdopcionController::class, 'responder'])
            ->name('solicitudes.responder');
        Route::post('solicitudes/{solicitud}/adoptar', [AdopcionController::class, 'store'])
            ->name('adopciones.store');

        Route::get('citas', [CitaAdopcionController::class, 'index'])->name('citas.index');
        Route::get('citas/{cita}', [CitaAdopcionController::class, 'show'])->name('citas.show');
        Route::patch('citas/{cita}', [CitaAdopcionController::class, 'update'])->name('citas.update');

        Route::patch('cumplimientos/{cumplimiento}', [CumplimientoRequisitoController::class, 'update'])
            ->name('cumplimientos.update');

        Route::get('adopciones', [AdopcionController::class, 'index'])->name('adopciones.index');
        Route::get('adopciones/{adopcion}', [AdopcionController::class, 'show'])->name('adopciones.show');
    });
