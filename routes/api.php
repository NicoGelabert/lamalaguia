<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoriaNegocioController;
use App\Http\Controllers\Api\NegocioController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\TramiteController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\SitioInteresController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categorias-negocio', CategoriaNegocioController::class);
Route::get('negocios', [NegocioController::class, 'index'])->name('api.negocios.index');
Route::get('negocios/{negocio}', [NegocioController::class, 'show'])->name('api.negocios.show');
Route::post('negocios', [NegocioController::class, 'store'])->name('api.negocios.store');
Route::match(['put', 'post'], 'negocios/{negocio}', [NegocioController::class, 'update'])->name('api.negocios.update');
Route::delete('negocios/{negocio}', [NegocioController::class, 'destroy'])->name('api.negocios.destroy');

Route::get('eventos', [EventoController::class, 'index'])->name('api.eventos.index');
Route::get('eventos/{evento}', [EventoController::class, 'show'])->name('api.eventos.show');
Route::post('eventos', [EventoController::class, 'store'])->name('api.eventos.store');
Route::put('eventos/{evento}', [EventoController::class, 'update'])->name('api.eventos.update');
Route::delete('eventos/{evento}', [EventoController::class, 'destroy'])->name('api.eventos.destroy');

Route::get('tramites', [TramiteController::class, 'index'])->name('api.tramites.index');
Route::get('tramites/{tramite}', [TramiteController::class, 'show'])->name('api.tramites.show');
Route::post('tramites', [TramiteController::class, 'store'])->name('api.tramites.store');
Route::put('tramites/{tramite}', [TramiteController::class, 'update'])->name('api.tramites.update');
Route::delete('tramites/{tramite}', [TramiteController::class, 'destroy'])->name('api.tramites.destroy');

Route::post('chat', ChatController::class);

Route::apiResource('sitios-interes', SitioInteresController::class)
    ->parameters(['sitios-interes' => 'sitioInteres'])
    ->names('api.sitios-interes');