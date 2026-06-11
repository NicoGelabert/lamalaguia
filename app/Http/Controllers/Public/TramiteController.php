<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TramiteController extends Controller
{
    private const CATEGORIAS = ['documentación', 'salud', 'trabajo', 'vivienda', 'educación'];

    public function index(Request $request)
    {
        $categoria = $request->input('categoria');

        $tramites = Tramite::query()
            ->where('activo', true)
            ->when(
                is_string($categoria) && in_array($categoria, self::CATEGORIAS, true),
                fn ($query) => $query->where('categoria', $categoria)
            )
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get();

        return Inertia::render('Public/Tramites/Index', [
            'tramites' => $tramites->map(fn (Tramite $tramite) => [
                'id' => $tramite->id,
                'slug' => $tramite->slug,
                'titulo' => $tramite->titulo,
                'categoria' => $tramite->categoria,
                'imagen_url' => $tramite->imagen ? asset('storage/' . $tramite->imagen) : null,
            ])->values(),
            'categorias' => self::CATEGORIAS,
            'categoriaActiva' => is_string($categoria) && in_array($categoria, self::CATEGORIAS, true)
                ? $categoria
                : null,
        ]);
    }

    public function show(string $slug)
    {
        $tramite = Tramite::query()
            ->where('slug', $slug)
            ->where('activo', true)
            ->firstOrFail();

        return Inertia::render('Public/Tramites/Show', [
            'tramite' => [
                'id' => $tramite->id,
                'slug' => $tramite->slug,
                'titulo' => $tramite->titulo,
                'categoria' => $tramite->categoria,
                'contenido' => $tramite->contenido,
                'imagen_url' => $tramite->imagen ? asset('storage/' . $tramite->imagen) : null,
            ],
        ]);
    }
}
