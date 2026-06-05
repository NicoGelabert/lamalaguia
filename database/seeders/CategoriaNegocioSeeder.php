<?php

namespace Database\Seeders;

use App\Models\CategoriaNegocio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriaNegocioSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Alimentación',
            'Gastronomía',
            'Gestoría y Trámites',
            'Salud',
            'Envíos a Argentina',
            'Inmobiliaria',
            'Trabajo y Empleo',
            'Educación',
            'Entretenimiento',
            'Otros servicios',
        ];

        foreach ($categorias as $nombre) {
            CategoriaNegocio::create([
                'nombre' => $nombre,
                'slug' => Str::slug($nombre),
            ]);
        }
    }
}