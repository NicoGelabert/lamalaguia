<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Tramite;
use Illuminate\Http\Request;

class TramiteController extends Controller
{
    public function index()
    {
        return Inertia::render('Tramites/Index');
    }

    public function create()
    {
        return Inertia::render('Tramites/Create');
    }

    public function edit(int $id)
    {
        return Inertia::render('Tramites/Edit', ['id' => $id]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'    => ['required', 'string', 'max:150'],
            'slug'      => ['required', 'string', 'max:150'],
            'contenido' => ['required', 'string'],
            'categoria' => ['required', 'string', 'in:Documentación,Salud,Trabajo,Vivienda,Educación'],
            'orden'     => ['nullable', 'integer'],
            'activo'    => ['required', 'boolean'],
        ]);

        Tramite::create($data);

        return redirect()->route('admin.tramites.index');
    }
}
