<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NegocioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', Rule::unique('negocios', 'slug')->ignore($this->route('negocio'))],
            'descripcion' => ['nullable', 'string'],
            'descripcion_corta' => ['nullable', 'string', 'max:160'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'web' => ['nullable', 'url', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'activo' => ['required', 'boolean'],
            'destacado' => ['required', 'boolean'],
            'orden_destacado' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'categoria_negocio_id' => ['required', 'exists:categoria_negocios,id'],
            'logo' => ['nullable', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
            'imagenes' => ['nullable', 'array'],
            'imagenes.*' => ['mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
        ];
    }
}