<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Negocio;
use Illuminate\Http\Request;

class NegocioController extends Controller
{
    public function index()
    {
        return Inertia::render('Negocios/Index');
    }

    public function create()
    {
        return Inertia::render('Negocios/Create');
    }

    public function edit(int $id)
    {
        return Inertia::render('Negocios/Edit', ['id' => $id]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'direccion' => ['nullable', 'string'],
            'ciudad' => ['nullable', 'string'],
            'telefono' => ['nullable', 'string'],
            'whatsapp' => ['nullable', 'string'],
            'web' => ['nullable', 'url'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'activo' => ['required', 'boolean'],
            'categoria_negocio_id' => ['required', 'exists:categoria_negocios,id'],
        ]);

        Negocio::create($data);

        return redirect()->route('admin.negocios.index');
    }
}