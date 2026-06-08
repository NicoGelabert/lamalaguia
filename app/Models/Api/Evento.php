<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Evento extends \App\Models\Evento
{
    public function getRouteKeyName()
    {
        return 'id';
    }
}
