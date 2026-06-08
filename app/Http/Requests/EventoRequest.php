<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:150'],
            'slug'        => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'fecha'       => ['required', 'date'],
            'lugar'       => ['nullable', 'string', 'max:255'],
            'url_externo' => ['nullable', 'url', 'max:255'],
            'activo'      => ['required', 'boolean'],
            'imagen'      => ['nullable', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
            'imagenes'    => ['nullable', 'array'],
            'imagenes.*'  => ['mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
        ];
    }
}
