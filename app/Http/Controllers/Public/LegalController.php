<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class LegalController extends Controller
{
    private function shared(): array
    {
        return [
            'siteName' => config('app.name'),
            'siteUrl' => config('app.url'),
            'contactEmail' => config('legal.contact_email'),
            'holder' => config('legal.holder'),
        ];
    }

    public function avisoLegal()
    {
        return Inertia::render('Public/Legal/AvisoLegal', $this->shared());
    }

    public function privacidad()
    {
        return Inertia::render('Public/Legal/Privacidad', $this->shared());
    }

    public function cookies()
    {
        return Inertia::render('Public/Legal/Cookies', $this->shared());
    }

    public function terminos()
    {
        return Inertia::render('Public/Legal/Terminos', $this->shared());
    }
}
