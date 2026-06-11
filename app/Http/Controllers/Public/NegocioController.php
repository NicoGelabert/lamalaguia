<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CategoriaNegocio;
use App\Models\Negocio;
use App\Support\Geo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NegocioController extends Controller
{
    public function index(Request $request)
    {
        $categoriaId = $request->filled('categoria') ? (int) $request->input('categoria') : null;
        $userLat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $userLng = $request->filled('lng') ? (float) $request->input('lng') : null;

        $negocios = Negocio::query()
            ->where('activo', true)
            ->whereNotNull('slug')
            ->with('categoria')
            ->when($categoriaId, fn ($query) => $query->where('categoria_negocio_id', $categoriaId))
            ->get();

        if (Geo::isValidCoordinate($userLat, $userLng)) {
            $negocios = Geo::sortByDistance($negocios, $userLat, $userLng);
        } else {
            $negocios = $negocios
                ->sortBy(fn (Negocio $negocio) => [
                    $negocio->destacado ? 0 : 1,
                    $negocio->orden_destacado ?? 9999,
                    $negocio->nombre,
                ])
                ->values();
        }

        $categorias = CategoriaNegocio::query()
            ->whereHas('negocios', fn ($query) => $query->where('activo', true))
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return Inertia::render('Public/Negocios/Index', [
            'negocios' => $negocios->map(fn (Negocio $negocio) => [
                'id' => $negocio->id,
                'slug' => $negocio->slug,
                'nombre' => $negocio->nombre,
                'ciudad' => $negocio->ciudad,
                'categoria' => $negocio->categoria?->nombre,
                'descripcion_corta' => $negocio->descripcion_corta,
                'logo_url' => $negocio->logo ? asset('storage/' . $negocio->logo) : null,
                'destacado' => $negocio->destacado,
                'distancia_km' => $negocio->distancia_km ?? null,
            ])->values(),
            'categorias' => $categorias->map(fn (CategoriaNegocio $categoria) => [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
            ])->values(),
            'categoriaActiva' => $categoriaId,
            'ordenadoPorCercania' => Geo::isValidCoordinate($userLat, $userLng),
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $userLat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $userLng = $request->filled('lng') ? (float) $request->input('lng') : null;

        $negocio = Negocio::query()
            ->where('slug', $slug)
            ->where('activo', true)
            ->with(['categoria', 'imagenes'])
            ->firstOrFail();

        $distanciaKm = null;
        if (
            Geo::isValidCoordinate($userLat, $userLng)
            && $negocio->lat !== null
            && $negocio->lng !== null
        ) {
            $distanciaKm = round(Geo::distanceKm(
                $userLat,
                $userLng,
                (float) $negocio->lat,
                (float) $negocio->lng
            ), 1);
        }

        return Inertia::render('Public/Negocios/Show', [
            'negocio' => [
                'id' => $negocio->id,
                'slug' => $negocio->slug,
                'nombre' => $negocio->nombre,
                'ciudad' => $negocio->ciudad,
                'direccion' => $negocio->direccion,
                'categoria' => $negocio->categoria?->nombre,
                'descripcion' => $negocio->descripcion,
                'descripcion_corta' => $negocio->descripcion_corta,
                'telefono' => $negocio->telefono,
                'whatsapp' => $negocio->whatsapp,
                'web' => $negocio->web,
                'lat' => $negocio->lat ? (float) $negocio->lat : null,
                'lng' => $negocio->lng ? (float) $negocio->lng : null,
                'place_id' => $negocio->place_id,
                'destacado' => $negocio->destacado,
                'logo_url' => $negocio->logo ? asset('storage/' . $negocio->logo) : null,
                'imagenes' => $negocio->imagenes->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => asset('storage/' . $img->ruta),
                ])->values(),
                'distancia_km' => $distanciaKm,
            ],
        ]);
    }
}
