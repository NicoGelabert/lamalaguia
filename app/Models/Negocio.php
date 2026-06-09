<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\CategoriaNegocio;

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
        'place_id',
        'activo',
        'logo',
        'logo_mime',
        'logo_size',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaNegocio::class, 'categoria_negocio_id', 'id');
    }

    public function imagenes()
    {
        return $this->hasMany(NegocioImagen::class)->orderBy('orden');
    }
}
