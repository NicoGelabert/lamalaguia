<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TramiteRequest;
use App\Http\Resources\TramiteListResource;
use App\Http\Resources\TramiteResource;
use App\Models\Tramite;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TramiteController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Tramite::query()
            ->where('titulo', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return TramiteListResource::collection($query);
    }

    public function store(TramiteRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data = array_merge($data, $this->saveImagen($request->file('logo')));
        }

        $tramite = Tramite::create($data);

        return new TramiteResource($tramite);
    }

    public function show(Tramite $tramite)
    {
        return new TramiteResource($tramite);
    }

    public function update(TramiteRequest $request, Tramite $tramite)
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            // Borrar imagen vieja
            if ($tramite->imagen) {
                Storage::delete('public/' . $tramite->imagen);
            }
            $data = array_merge($data, $this->saveImagen($request->file('imagen')));
        }

        $tramite->update($data);

        return new TramiteResource($tramite);
    }

    public function destroy(Tramite $tramite)
    {
        // Borrar imagen
        if ($tramite->imagen) {
            Storage::delete('public/' . $tramite->imagen);
        }

        $tramite->delete();

        return response()->noContent();
    }

    private function saveImagen(UploadedFile $file): array
    {
        $path = 'tramites/imagen/' . Str::random(10);
        Storage::disk('public')->makeDirectory($path);
        $filename = $file->getClientOriginalName();
        Storage::disk('public')->putFileAs($path, $file, $filename);

        return [
            'imagen' => $path . '/' . $filename,
            'imagen_mime' => $file->getClientMimeType(),
            'imagen_size' => $file->getSize(),
        ];
    }
}
