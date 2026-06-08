<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\EventoImagen;

class Evento extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'fecha',
        'lugar',
        'url_externo',
        'activo',
        'imagen',
        'imagen_mime',
        'imagen_size',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function imagenes()
    {
        return $this->hasMany(EventoImagen::class)->orderBy('orden');
    }
}
