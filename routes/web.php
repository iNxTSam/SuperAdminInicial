<?php

use App\Http\Controllers\Login;
use App\Http\Controllers\Logout;
use App\Http\Controllers\Register;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\VigilanteController;

//Ruta por defecto → dashboard del SuperAdmin
Route::get('/', function  (){return view('login');})->name('login');
Route::get('/registro', [register::class,'view'])->name('register');

Route::post('/logging',[Login::class,'loginUser'])->name('loginUsuario');

Route::post('/usuarios',[Register::class,'crear'])->name('registrarUsuario');

Route::get('/vigilante',[VigilanteController::class, 'dashboard'])->name('vigilante.dashboard');
Route::get('/entradas-salidas',[VigilanteController::class, 'vehicles'])->name('vigilante.vehicles');

Route::middleware(['auth','rol_id:2','no-cache'])->prefix('vigilante')->name('vigilante.')->group(function(){
});

// Grupo de rutas del SuperAdmin
Route::middleware(['auth','rol_id:1','no-cache'])->prefix('superadmin')->name('superadmin.')->group(function () {

    // Dashboard 
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

Route::post ('/logout',[Logout::class,'logout'])->name('logout');
