<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Negocio;
use App\Models\Tramite;
use App\Support\Geo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $proximosEventos = Evento::query()
            ->where('activo', true)
            ->whereNotNull('slug')
            ->where('fecha', '>=', now())
            ->orderBy('fecha')
            ->take(3)
            ->get();

        $tramitesPopulares = Tramite::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->take(6)
            ->get();

        $negociosDestacados = Negocio::query()
            ->where('activo', true)
            ->whereNotNull('slug')
            ->where('destacado', true)
            ->with('categoria')
            ->get();

        $userLat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $userLng = $request->filled('lng') ? (float) $request->input('lng') : null;

        if (Geo::isValidCoordinate($userLat, $userLng)) {
            $negociosDestacados = Geo::sortByDistance($negociosDestacados, $userLat, $userLng);
        } else {
            $negociosDestacados = $negociosDestacados->sortBy('orden_destacado')->values();
        }

        $negociosDestacados = $negociosDestacados->take(6);

        return Inertia::render('Home', [
            'stats' => [
                'negocios' => Negocio::where('activo', true)->count(),
                'tramites' => Tramite::where('activo', true)->count(),
                'eventos' => Evento::where('activo', true)->where('fecha', '>=', now())->count(),
            ],
            'proximosEventos' => $proximosEventos->map(fn (Evento $evento) => [
                'id' => $evento->id,
                'slug' => $evento->slug,
                'nombre' => $evento->nombre,
                'fecha' => $evento->fecha->format('d/m/Y H:i'),
                'lugar' => $evento->lugar,
            ])->values(),
            'tramitesPopulares' => $tramitesPopulares->map(fn (Tramite $tramite) => [
                'id' => $tramite->id,
                'slug' => $tramite->slug,
                'titulo' => $tramite->titulo,
                'categoria' => $tramite->categoria,
            ])->values(),
            'negociosDestacados' => $negociosDestacados->map(fn (Negocio $negocio) => [
                'id' => $negocio->id,
                'slug' => $negocio->slug,
                'nombre' => $negocio->nombre,
                'ciudad' => $negocio->ciudad,
                'categoria' => $negocio->categoria?->nombre,
                'descripcion_corta' => $negocio->descripcion_corta,
                'logo_url' => $negocio->logo ? asset('storage/' . $negocio->logo) : null,
                'distancia_km' => $negocio->distancia_km ?? null,
            ])->values(),
            'ordenadoPorCercania' => Geo::isValidCoordinate($userLat, $userLng),
        ]);
    }
}
