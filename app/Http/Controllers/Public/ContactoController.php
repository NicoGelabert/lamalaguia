<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactoRecibido;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactoController extends Controller
{
    public function index()
    {
        return Inertia::render('Public/Contacto/Index', [
            'contactEmail' => config('legal.contact_email'),
        ]);
    }

    public function store(ContactRequest $request)
    {
        $data = $request->validated();

        Mail::to(config('legal.contact_email'))->send(new ContactoRecibido(
            nombre: $data['nombre'],
            email: $data['email'],
            asunto: $data['asunto'] ?? 'Consulta desde La Malaguía',
            mensaje: $data['mensaje'],
        ));

        return back()->with('success', '¡Mensaje enviado! Te responderemos a la brevedad.');
    }
}
