<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\Evento;
use App\Models\Tramite;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                ['titulo' => 'Negocios', 'total' => Negocio::count()],
                ['titulo' => 'Eventos', 'total' => Evento::count()],
                ['titulo' => 'Trámites', 'total' => Tramite::count()],
            ],
        ]);
    }
}