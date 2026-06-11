<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SitioInteresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', Rule::unique('sitios_interes', 'slug')->ignore($this->route('sitioInteres'))],
            'tipo' => ['required', 'string', Rule::in(['ayuntamiento', 'oficina_extranjeria', 'registro_civil', 'comisaria', 'otro'])],
            'descripcion' => ['nullable', 'string'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'web' => ['nullable', 'url', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['required', 'boolean'],
        ];
    }
}
