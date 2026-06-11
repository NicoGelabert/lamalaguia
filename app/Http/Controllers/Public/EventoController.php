<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::query()
            ->where('activo', true)
            ->whereNotNull('slug')
            ->where('fecha', '>=', now())
            ->orderBy('fecha')
            ->get();

        return Inertia::render('Public/Eventos/Index', [
            'eventos' => $eventos->map(fn (Evento $evento) => [
                'id' => $evento->id,
                'slug' => $evento->slug,
                'nombre' => $evento->nombre,
                'fecha' => $evento->fecha->format('d/m/Y H:i'),
                'lugar' => $evento->lugar,
                'descripcion' => $evento->descripcion
                    ? Str::limit(strip_tags($evento->descripcion), 140)
                    : null,
                'imagen_url' => $evento->imagen ? asset('storage/' . $evento->imagen) : null,
            ])->values(),
        ]);
    }

    public function show(string $slug)
    {
        $evento = Evento::query()
            ->where('slug', $slug)
            ->where('activo', true)
            ->with('imagenes')
            ->firstOrFail();

        return Inertia::render('Public/Eventos/Show', [
            'evento' => [
                'id' => $evento->id,
                'slug' => $evento->slug,
                'nombre' => $evento->nombre,
                'fecha' => $evento->fecha->format('d/m/Y H:i'),
                'lugar' => $evento->lugar,
                'descripcion' => $evento->descripcion,
                'url_externo' => $evento->url_externo,
                'lat' => $evento->lat,
                'lng' => $evento->lng,
                'place_id' => $evento->place_id,
                'imagen_url' => $evento->imagen ? asset('storage/' . $evento->imagen) : null,
                'imagenes' => $evento->imagenes->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => asset('storage/' . $img->ruta),
                ])->values(),
            ],
        ]);
    }
}
