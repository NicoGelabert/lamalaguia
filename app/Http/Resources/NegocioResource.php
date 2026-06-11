<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NegocioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'descripcion' => $this->descripcion,
            'descripcion_corta' => $this->descripcion_corta,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'telefono' => $this->telefono,
            'whatsapp' => $this->whatsapp,
            'web' => $this->web,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'place_id' => $this->place_id,
            'activo' => (bool) $this->activo,
            'destacado' => (bool) $this->destacado,
            'orden_destacado' => $this->orden_destacado,
            'categoria_negocio_id' => $this->categoria_negocio_id,
            'categoria' => $this->categoria?->nombre,
            'logo_url' => $this->logo ? asset('storage/' . $this->logo) : null,
            'imagenes' => $this->imagenes?->map(fn($img) => [
                'id' => $img->id,
                'url' => asset('storage/' . $img->ruta),
                'orden' => $img->orden,
            ]),
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
