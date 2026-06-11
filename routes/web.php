<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SitioInteresController;
use App\Http\Controllers\Public\EventoController as PublicEventoController;
use App\Http\Controllers\Public\NegocioController as PublicNegocioController;
use App\Http\Controllers\Public\TramiteController as PublicTramiteController;

Route::get('/', HomeController::class)->name('home');
Route::get('/tramites', [PublicTramiteController::class, 'index'])->name('tramites.index');
Route::get('/tramites/{slug}', [PublicTramiteController::class, 'show'])->name('tramites.show');
Route::get('/negocios', [PublicNegocioController::class, 'index'])->name('negocios.index');
Route::get('/negocios/{slug}', [PublicNegocioController::class, 'show'])->name('negocios.show');
Route::get('/eventos', [PublicEventoController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{slug}', [PublicEventoController::class, 'show'])->name('eventos.show');

Route::get('/laravel', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'lamalaguiaVersion' => config('app.version'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('negocios', NegocioController::class);
    Route::resource('eventos', EventoController::class);
    Route::resource('tramites', TramiteController::class);
    Route::resource('sitios-interes', SitioInteresController::class)->only(['index', 'create', 'edit']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
