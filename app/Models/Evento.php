<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha',
        'lugar',
        'url_externo',
        'activo',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];
}
