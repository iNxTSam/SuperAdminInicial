<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;

// 🔹 Ruta por defecto → dashboard del SuperAdmin
Route::get('/', [SuperAdminController::class, 'dashboard'])->name('home');

// 🔹 Grupo de rutas del SuperAdmin
Route::prefix('superadmin')->name('superadmin.')->group(function () {

    // Dashboard (también accesible en /superadmin/dashboard)
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Tarifas
    Route::get('/tarifas', [SuperAdminController::class, 'tarifas'])->name('tarifas');
    Route::post('/tarifas', [SuperAdminController::class, 'storeTarifa'])->name('tarifas.store');
    Route::put('/tarifas/{id}', [SuperAdminController::class, 'updateTarifa'])->name('tarifas.update');

    // Bahías
    Route::get('/bahias', [SuperAdminController::class, 'bahias'])->name('bahias');
    Route::post('/bahias', [SuperAdminController::class, 'storeBahia'])->name('bahias.store');
    Route::put('/bahias/{id}', [SuperAdminController::class, 'updateBahia'])->name('bahias.update');

    // Reportes
    Route::get('/reportes', [SuperAdminController::class, 'reportes'])->name('reportes');
    Route::post('/reportes/generar', [SuperAdminController::class, 'generarReporte'])->name('reportes.generar');

    // Configuración
    Route::get('/configuracion', [SuperAdminController::class, 'configuracion'])->name('configuracion');
    Route::post('/configuracion/tipo-vehiculo', [SuperAdminController::class, 'storeTipoVehiculo'])->name('configuracion.tipoVehiculo');
});
