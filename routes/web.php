<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\ReporteController; // 1. Importar el nuevo controlador de reportes

// --- RUTA PRINCIPAL ---
Route::get('/', [ClienteController::class, 'index'])->name('home');

// --- RUTAS DE RECURSOS PARA CADA MÓDULO ---
Route::resource('clientes', ClienteController::class);
Route::resource('vehiculos', VehiculoController::class);
Route::resource('repuestos', RepuestoController::class);

// --- RUTAS PARA ÓRDENES DE TRABAJO ---
// Crea rutas para: index (listar), store (guardar), destroy (eliminar).
Route::resource('ordenes', OrdenTrabajoController::class)->only(['index', 'store', 'destroy']);

// Rutas explícitas para las acciones de gestión que son POST
Route::post('ordenes/agregar-repuesto/{orden_id}', [OrdenTrabajoController::class, 'agregarRepuesto'])->name('ordenes.agregarRepuesto');
Route::post('ordenes/actualizar-estado/{orden_id}', [OrdenTrabajoController::class, 'actualizarEstado'])->name('ordenes.actualizarEstado');


// --- RUTAS PARA REPORTES ---
// 2. Añadir las nuevas rutas para la sección de reportes
Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::post('reportes/ordenes-por-cliente', [ReporteController::class, 'ordenesPorCliente'])->name('reportes.ordenesPorCliente');