<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TramiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'titulo'     => $this->titulo,
            'slug'       => $this->slug,
            'contenido'  => $this->contenido,
            'categoria'  => $this->categoria,
            'orden'      => $this->orden,
            'activo'     => (bool) $this->activo,
            'imagen_url' => $this->imagen ? asset('storage/' . $this->imagen) : null,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
