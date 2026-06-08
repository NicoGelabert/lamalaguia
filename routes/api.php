<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NegocioController;
use App\Http\Controllers\Api\CategoriaNegocioController;
use App\Http\Controllers\Api\EventoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categorias-negocio', CategoriaNegocioController::class);
Route::get('negocios', [NegocioController::class, 'index'])->name('api.negocios.index');
Route::get('negocios/{negocio}', [NegocioController::class, 'show'])->name('api.negocios.show');
Route::post('negocios', [NegocioController::class, 'store'])->name('api.negocios.store');
Route::put('negocios/{negocio}', [NegocioController::class, 'update'])->name('api.negocios.update');
Route::delete('negocios/{negocio}', [NegocioController::class, 'destroy'])->name('api.negocios.destroy');

Route::get('eventos', [EventoController::class, 'index'])->name('api.eventos.index');
Route::get('eventos/{evento}', [EventoController::class, 'show'])->name('api.eventos.show');
Route::post('eventos', [EventoController::class, 'store'])->name('api.eventos.store');
Route::put('eventos/{evento}', [EventoController::class, 'update'])->name('api.eventos.update');
Route::delete('eventos/{evento}', [EventoController::class, 'destroy'])->name('api.eventos.destroy');