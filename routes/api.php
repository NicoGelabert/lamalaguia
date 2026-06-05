<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NegocioController;
use App\Http\Controllers\Api\CategoriaNegocioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categorias-negocio', CategoriaNegocioController::class);
Route::get('negocios', [NegocioController::class, 'index'])->name('api.negocios.index');
Route::get('negocios/{negocio}', [NegocioController::class, 'show'])->name('api.negocios.show');
Route::post('negocios', [NegocioController::class, 'store'])->name('api.negocios.store');
Route::put('negocios/{negocio}', [NegocioController::class, 'update'])->name('api.negocios.update');
Route::delete('negocios/{negocio}', [NegocioController::class, 'destroy'])->name('api.negocios.destroy');

