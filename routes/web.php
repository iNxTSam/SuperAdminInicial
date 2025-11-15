<?php

use App\Http\Controllers\Login;
use App\Http\Controllers\Logout;
use App\Http\Controllers\Register;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\VigilanteController;
use App\Http\Controllers\AdminController;


Route::get('/', function () {
    return view('login');
})->name('login');

Route::get('/registro', [Register::class, 'view'])->name('register');
Route::post('/logging', [Login::class, 'loginUser'])->name('loginUsuario');
Route::post('/usuarios', [Register::class, 'crear'])->name('registrarUsuario');

Route::post('/logout', [Logout::class, 'logout'])->name('logout');




Route::middleware(['auth', 'rol_id:3', 'no-cache'])->prefix('vigilante')->name('vigilante.')->group(function () {
    //  RUTAS DEL VIGILANTE

    Route::get('/', [VigilanteController::class, 'dashboard'])->name('dashboard');
    Route::get('/entradas-salidas', [VigilanteController::class, 'vehicles'])->name('vehicles');
    Route::post('/verificar-usuario', [VigilanteController::class, 'verificarUsuario'])->name('verificarUsuario');

    Route::get('/vehiculos', [VigilanteController::class, 'gestion'])->name('gestionvehiculos');
    Route::post('/vehiculos',[VigilanteController::class, 'nuevoVehiculo'])->name('nuevo.vehiculo');
    Route::put('/vehiculos/{id}',[VigilanteController::class, 'updateVehiculo'])->name('update.vehiculo');
});


//  RUTAS DEL SUPER ADMIN

Route::middleware(['auth', 'rol_id:1', 'no-cache'])->prefix('superadmin')->name('superadmin.')->group(function () {

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
    Route::delete('/bahias/eliminar/{id}', [SuperAdminController::class, 'deleteBahia'])->name('bahias.delete');

    // Reportes
    Route::get('/reportes', [SuperAdminController::class, 'reportes'])->name('reportes');
    Route::post('/reportes/generar', [SuperAdminController::class, 'generarReporte'])->name('reportes.generar');

    // Configuración
    Route::get('/configuracion', [SuperAdminController::class, 'configuracion'])->name('configuracion');
    Route::post('/configuracion/tipo-vehiculo', [SuperAdminController::class, 'storeTipoVehiculo'])->name('configuracion.tipoVehiculo');
});


//  RUTAS DEL ADMINISTRADOR

Route::middleware(['auth', 'rol_id:2', 'no-cache'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Usuarios
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
    Route::post('/usuarios', [AdminController::class, 'storeUsuario'])->name('usuarios.store');
    Route::put('/usuarios/{id}', [AdminController::class, 'updateUsuario'])->name('usuarios.update');
    Route::patch('/usuarios/{id}/toggle', [AdminController::class, 'toggleUsuario'])->name('usuarios.toggle');

    // Contratos
    Route::get('/contratos', [AdminController::class, 'contratos'])->name('contratos');
    Route::post('/contratos', [AdminController::class, 'storeContrato'])->name('contratos.store');
    Route::put('/contratos/{id}', [AdminController::class, 'updateContrato'])->name('contratos.update');
    Route::post('/contratos/{id}/renovar', [AdminController::class, 'renovarContrato'])->name('contratos.renovar');


});

