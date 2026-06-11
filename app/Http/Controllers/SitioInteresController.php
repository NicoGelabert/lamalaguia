<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class SitioInteresController extends Controller
{
    public function index()
    {
        return Inertia::render('SitiosInteres/Index');
    }

    public function create()
    {
        return Inertia::render('SitiosInteres/Create');
    }

    public function edit(int $id)
    {
        return Inertia::render('SitiosInteres/Edit', ['id' => $id]);
    }
}
