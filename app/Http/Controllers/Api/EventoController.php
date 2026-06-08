<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventoRequest;
use App\Http\Resources\EventoListResource;
use App\Http\Resources\EventoResource;
use App\Models\Evento;
use App\Models\EventoImagen;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventoController extends Controller
{
    public function index()
    {
        $perPage       = request('per_page', 10);
        $search        = request('search', '');
        $sortField     = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Evento::query()
            ->where('nombre', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return EventoListResource::collection($query);
    }

    public function store(EventoRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data = array_merge($data, $this->saveImagen($request->file('imagen')));
        }

        $evento = Evento::create($data);

        if ($request->hasFile('imagenes')) {
            $this->saveGaleria($evento, $request->file('imagenes'));
        }

        return new EventoResource($evento->load(['imagenes']));
    }

    public function show(Evento $evento)
    {
        return new EventoResource($evento->load(['imagenes']));
    }

    public function update(EventoRequest $request, Evento $evento)
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($evento->imagen) {
                Storage::disk('public')->delete($evento->imagen);
            }
            $data = array_merge($data, $this->saveImagen($request->file('imagen')));
        }

        $evento->update($data);

        if ($request->hasFile('imagenes')) {
            $this->saveGaleria($evento, $request->file('imagenes'));
        }

        return new EventoResource($evento->load(['imagenes']));
    }

    public function destroy(Evento $evento)
    {
        if ($evento->logo) {
            Storage::disk('public')->delete($evento->logo);
        }
        foreach ($evento->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->ruta);
        }
        $evento->delete();
        return response()->noContent();
    }

    private function saveImagen(UploadedFile $file): array
    {
        $path     = 'eventos/imagenes/' . Str::random(10);
        Storage::disk('public')->makeDirectory($path);
        $filename = $file->getClientOriginalName();
        Storage::disk('public')->putFileAs($path, $file, $filename);

        return [
            'imagen'      => $path . '/' . $filename,
            'imagen_mime' => $file->getClientMimeType(),
            'imagen_size' => $file->getSize(),
        ];
    }

    private function saveGaleria(Evento $evento, array $files): void
    {
        foreach ($files as $index => $file) {
            $path     = 'eventos/' . $evento->id . '/galeria/' . Str::random(10);
            Storage::disk('public')->makeDirectory($path);
            $filename = $file->getClientOriginalName();
            Storage::disk('public')->putFileAs($path, $file, $filename);

            EventoImagen::create([
                'evento_id' => $evento->id,
                'ruta'      => $path . '/' . $filename,
                'mime'      => $file->getClientMimeType(),
                'size'      => $file->getSize(),
                'orden'     => $index,
            ]);
        }
    }
}
