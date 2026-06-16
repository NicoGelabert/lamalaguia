<?php

namespace App\Http\Requests;

use App\Support\RedSocialTipo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NegocioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('redes_sociales')) {
            return;
        }

        $value = $this->input('redes_sociales');

        if (! is_string($value)) {
            return;
        }

        if ($value === '' || $value === '[]') {
            $this->merge(['redes_sociales' => null]);

            return;
        }

        $decoded = json_decode($value, true);

        $this->merge([
            'redes_sociales' => is_array($decoded) ? $decoded : null,
        ]);
    }

    public static function normalizeRedesSociales(mixed $value): ?array
    {
        if ($value === null || $value === '' || $value === '[]') {
            return null;
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value) || $value === []) {
            return null;
        }

        $normalized = [];

        foreach ($value as $item) {
            if (! is_array($item) || empty($item['tipo']) || empty($item['url'])) {
                continue;
            }

            $normalized[] = [
                'tipo' => $item['tipo'],
                'url' => $item['url'],
            ];
        }

        return $normalized === [] ? null : array_values($normalized);
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
            'redes_sociales' => ['nullable', 'array', 'max:7'],
            'redes_sociales.*.tipo' => ['required', 'string', Rule::in(RedSocialTipo::TIPOS)],
            'redes_sociales.*.url' => ['required', 'url', 'max:500'],
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