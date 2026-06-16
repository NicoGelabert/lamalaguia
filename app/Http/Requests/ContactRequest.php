<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'asunto' => ['nullable', 'string', 'max:150'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
        ];
    }
}
