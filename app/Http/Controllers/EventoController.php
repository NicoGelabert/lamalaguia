<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Evento;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        return Inertia::render('Eventos/Index');
    }

    public function create()
    {
        return Inertia::render('Eventos/Create');
    }

    public function edit(int $id)
    {
        return Inertia::render('Eventos/Edit', ['id' => $id]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'fecha'       => ['required', 'date'],
            'lugar'       => ['nullable', 'string', 'max:255'],
            'url_externo' => ['nullable', 'url', 'max:255'],
            'activo'      => ['required', 'boolean'],
        ]);

        Evento::create($data);

        return redirect()->route('admin.eventos.index');
    }
}