<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaNegocio extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'icono',
    ];

    public function negocios()
    {
        return $this->hasMany(Negocio::class);
    }
}
