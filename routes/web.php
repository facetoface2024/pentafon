<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SistemaQrController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [SistemaQrController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/planes', function () {
    return Inertia::render('Planes');
})->middleware(['auth', 'verified'])->name('planes');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Sistema QR routes
    Route::get('/plantilla-clientes', [SistemaQrController::class, 'descargarPlantilla'])->name('clientes.plantilla');
    Route::post('/subir-excel', [SistemaQrController::class, 'subirExcel'])->name('clientes.subir-excel');
    Route::post('/generar-qr/{id}', [SistemaQrController::class, 'generarQr'])->name('clientes.generar-qr');
    Route::post('/guardar-qr', [SistemaQrController::class, 'guardarQr'])->name('clientes.guardar-qr');
    Route::patch('/toggle-qr/{id}', [SistemaQrController::class, 'toggleQrEstado'])->name('clientes.toggle-qr');
    Route::delete('/clientes/{id}', [SistemaQrController::class, 'eliminarCliente'])->name('clientes.eliminar');
    Route::get('/exportar-clientes', [SistemaQrController::class, 'exportarClientes'])->name('clientes.exportar');
    Route::get('/scanner', [SistemaQrController::class, 'scanner'])->name('qr.scanner');
});

// Rutas públicas para QR
Route::get('/saludo/{token}', [SistemaQrController::class, 'saludoCliente'])->name('cliente.saludo');

require __DIR__.'/auth.php';
