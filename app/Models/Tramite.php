<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'contenido',
        'categoria',
        'orden',
        'activo',
        'imagen',
        'imagen_mime',
        'imagen_size',
    ];
}
