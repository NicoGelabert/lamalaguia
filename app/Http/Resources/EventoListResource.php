<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoListResource extends JsonResource
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
            'nombre'     => $this->nombre,
            'slug'       => $this->slug,
            'fecha'      => (new \DateTime($this->fecha))->format('Y-m-d H:i'),
            'lugar'      => $this->lugar,
            'lat'        => $this->lat,
            'lng'        => $this->lng,
            'activo'     => (bool) $this->activo,
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
