<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NegocioRequest;
use App\Http\Resources\NegocioListResource;
use App\Http\Resources\NegocioResource;
use App\Models\Negocio;
use App\Models\NegocioImagen;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NegocioController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Negocio::query()
            ->with('categoria')
            ->where('nombre', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return NegocioListResource::collection($query);
    }

    public function store(NegocioRequest $request)
    {
        $data = $request->validated();
        $data['redes_sociales'] = NegocioRequest::normalizeRedesSociales($request->input('redes_sociales'));

        if ($request->hasFile('logo')) {
            $data = array_merge($data, $this->saveLogo($request->file('logo')));
        }

        $negocio = Negocio::create($data);

        if ($request->hasFile('imagenes')) {
            $this->saveImagenes($negocio, $request->file('imagenes'));
        }

        return new NegocioResource($negocio->load(['categoria', 'imagenes']));
    }

    public function show(Negocio $negocio)
    {
        return new NegocioResource($negocio->load(['categoria', 'imagenes']));
    }

    public function update(NegocioRequest $request, Negocio $negocio)
    {
        $data = $request->validated();
        $data['redes_sociales'] = NegocioRequest::normalizeRedesSociales(
            $request->input('redes_sociales', $data['redes_sociales'] ?? null)
        );

        if ($request->hasFile('logo')) {
            // Borrar logo viejo
            if ($negocio->logo) {
                Storage::delete('public/' . $negocio->logo);
            }
            $data = array_merge($data, $this->saveLogo($request->file('logo')));
        }

        $negocio->update($data);

        if ($request->hasFile('imagenes')) {
            $this->saveImagenes($negocio, $request->file('imagenes'));
        }

        return new NegocioResource($negocio->load(['categoria', 'imagenes']));
    }

    public function destroy(Negocio $negocio)
    {
        // Borrar logo
        if ($negocio->logo) {
            Storage::delete('public/' . $negocio->logo);
        }

        // Borrar imágenes
        foreach ($negocio->imagenes as $imagen) {
            Storage::delete('public/' . $imagen->ruta);
        }

        $negocio->delete();

        return response()->noContent();
    }

    private function saveLogo(UploadedFile $file): array
    {
        $path = 'negocios/logos/' . Str::random(10);
        Storage::disk('public')->makeDirectory($path);
        $filename = $file->getClientOriginalName();
        Storage::disk('public')->putFileAs($path, $file, $filename);

        return [
            'logo' => $path . '/' . $filename,
            'logo_mime' => $file->getClientMimeType(),
            'logo_size' => $file->getSize(),
        ];
    }

    private function saveImagenes(Negocio $negocio, array $files): void
    {
        foreach ($files as $index => $file) {
            $path = 'negocios/' . $negocio->id . '/imagenes/' . Str::random(10);
            Storage::disk('public')->makeDirectory($path);
            $filename = $file->getClientOriginalName();
            Storage::disk('public')->putFileAs($path, $file, $filename);

            NegocioImagen::create([
                'negocio_id' => $negocio->id,
                'ruta' => $path . '/' . $filename,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'orden' => $index,
            ]);
        }
    }
}