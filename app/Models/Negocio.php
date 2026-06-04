<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    protected $fillable = [
        'categoria_negocio_id',
        'nombre',
        'slug',
        'descripcion',
        'direccion',
        'ciudad',
        'telefono',
        'whatsapp',
        'web',
        'lat',
        'lng',
        'activo',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaNegocio::class);
    }
}
