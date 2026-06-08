<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'slug'        => $this->slug,
            'descripcion' => $this->descripcion,
            'fecha'       => (new \DateTime($this->fecha))->format('Y-m-d\TH:i'),
            'lugar'       => $this->lugar,
            'url_externo' => $this->url_externo,
            'activo'      => (bool) $this->activo,
            'imagen_url'  => $this->imagen ? asset('storage/' . $this->imagen) : null,
            'imagenes'    => $this->imagenes?->map(fn($img) => [
                'id'    => $img->id,
                'url'   => asset('storage/' . $img->ruta),
                'orden' => $img->orden,
            ]),
            'created_at'  => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at'  => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
