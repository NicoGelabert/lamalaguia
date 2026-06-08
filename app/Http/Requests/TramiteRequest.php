<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TramiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'    => ['required', 'string', 'max:150'],
            'slug'      => ['required', 'string', 'max:150'],
            'contenido' => ['required', 'string'],
            'categoria' => ['required', 'string', 'in:documentación,salud,trabajo,vivienda,educación'],
            'orden'     => ['nullable', 'integer'],
            'activo'    => ['required', 'boolean'],
        ];
    }
}
