<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitioInteres extends Model
{
    protected $table = 'sitios_interes';

    protected $fillable = [
        'nombre',
        'slug',
        'tipo',
        'descripcion',
        'direccion',
        'ciudad',
        'telefono',
        'web',
        'lat',
        'lng',
        'place_id',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
        'lat' => 'float',
        'lng' => 'float',
    ];
}
