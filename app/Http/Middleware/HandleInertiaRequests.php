<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'analytics' => [
                'google_id' => config('analytics.google_id'),
                'clarity_id' => config('analytics.clarity_id'),
                'enabled' => (bool) (config('analytics.google_id') || config('analytics.clarity_id')),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }
}
