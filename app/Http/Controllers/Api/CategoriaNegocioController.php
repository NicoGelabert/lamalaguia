<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaNegocioResource;
use App\Models\CategoriaNegocio;

class CategoriaNegocioController extends Controller
{
    public function index()
    {
        $categorias = CategoriaNegocio::orderBy('nombre')->get();
        return CategoriaNegocioResource::collection($categorias);
    }
}