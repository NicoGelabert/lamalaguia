<?php

namespace App\Models\Api;

class Negocio extends \App\Models\Negocio
{
    public function getRouteKeyName()
    {
        return 'id';
    }
    protected $with = ['categoria'];
}