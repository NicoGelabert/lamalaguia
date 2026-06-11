<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SitioInteresRequest;
use App\Http\Resources\SitioInteresListResource;
use App\Http\Resources\SitioInteresResource;
use App\Models\SitioInteres;

class SitioInteresController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'orden');
        $sortDirection = request('sort_direction', 'asc');

        $query = SitioInteres::query()
            ->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('ciudad', 'like', "%{$search}%")
                    ->orWhere('tipo', 'like', "%{$search}%");
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return SitioInteresListResource::collection($query);
    }

    public function store(SitioInteresRequest $request)
    {
        $sitio = SitioInteres::create($request->validated());

        return new SitioInteresResource($sitio);
    }

    public function show(SitioInteres $sitioInteres)
    {
        return new SitioInteresResource($sitioInteres);
    }

    public function update(SitioInteresRequest $request, SitioInteres $sitioInteres)
    {
        $sitioInteres->update($request->validated());

        return new SitioInteresResource($sitioInteres);
    }

    public function destroy(SitioInteres $sitioInteres)
    {
        $sitioInteres->delete();

        return response()->noContent();
    }
}
