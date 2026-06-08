<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Tramite extends \App\Models\Tramite
{
    public function getRouteKeyName()
    {
        return 'id';
    }
}
