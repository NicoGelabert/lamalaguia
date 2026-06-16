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
        'descripcion_corta',
        'direccion',
        'ciudad',
        'telefono',
        'whatsapp',
        'web',
        'redes_sociales',
        'lat',
        'lng',
        'place_id',
        'activo',
        'destacado',
        'orden_destacado',
        'logo',
        'logo_mime',
        'logo_size',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'destacado' => 'boolean',
        'orden_destacado' => 'integer',
        'redes_sociales' => 'array',
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
