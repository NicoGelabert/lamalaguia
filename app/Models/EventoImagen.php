<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoImagen extends Model
{
    protected $table = 'evento_imagenes';

    protected $fillable = [
        'evento_id',
        'ruta',
        'mime',
        'size',
        'orden',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
