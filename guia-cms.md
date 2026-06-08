# Guía CMS — La Malaguía
## Cómo agregar una entidad nueva de punta a punta

Esta guía toma como referencia lo que construimos para **Negocio**. Seguí estos pasos en orden para agregar Tramite, Evento, CategoriaNegocio, o cualquier entidad futura.

---

## Estructura general

```
app/
  Http/
    Controllers/
      NegocioController.php         ← Controller web (Inertia)
      Api/
        NegocioController.php       ← Controller API (JSON)
        CategoriaNegocioController.php
    Requests/
      NegocioRequest.php            ← Validación
    Resources/
      NegocioResource.php           ← Respuesta completa (show/edit)
      NegocioListResource.php       ← Respuesta resumida (tabla)
  Models/
    Negocio.php                     ← Modelo principal
    NegocioImagen.php               ← Modelo de imágenes
    CategoriaNegocio.php
    Api/
      Negocio.php                   ← Modelo API (extiende el principal)

resources/js/
  Pages/
    Negocios/
      Index.vue                     ← Lista con tabla
      Create.vue                    ← Formulario de creación
      Edit.vue                      ← Formulario de edición
      NegocioForm.vue               ← Formulario compartido
      NegociosTable.vue             ← Componente de tabla
  stores/
    negocio.ts                      ← Store Pinia

routes/
  web.php                           ← Rutas Inertia (con auth)
  api.php                           ← Rutas JSON
```

---

## Paso 1 — Modelo principal

```bash
php artisan make:model NombreEntidad -m
```

El modelo principal va en `app/Models/`. Definís:
- `$fillable` con todos los campos
- Relaciones (`belongsTo`, `hasMany`)
- Si tiene `belongsTo`, especificá la foreign key explícitamente:

```php
public function categoria()
{
    return $this->belongsTo(CategoriaNegocio::class, 'categoria_negocio_id', 'id');
}
```

> **Importante:** Si el nombre de la tabla no sigue la convención de Laravel (plural en inglés), especificá `protected $table = 'nombre_tabla'`.

---

## Paso 2 — Migration

En el archivo generado en `database/migrations/`, definís los campos dentro de `up()`:

```php
public function up(): void
{
    Schema::create('negocios', function (Blueprint $table) {
        $table->id();
        $table->foreignId('categoria_negocio_id')->constrained()->cascadeOnDelete();
        $table->string('nombre');
        $table->string('slug')->unique();
        $table->text('descripcion')->nullable();
        $table->string('logo')->nullable();
        $table->string('logo_mime')->nullable();
        $table->integer('logo_size')->nullable();
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
```

### Si la entidad tiene imágenes múltiples, crear tabla aparte:

```bash
php artisan make:model NombreEntidadImagen -m
```

```php
Schema::create('nombre_entidad_imagenes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('nombre_entidad_id')->constrained()->cascadeOnDelete();
    $table->string('ruta');
    $table->string('mime')->nullable();
    $table->integer('size')->nullable();
    $table->integer('orden')->default(0);
    $table->timestamps();
});
```

> **Importante:** Laravel pluraliza en inglés. Si el nombre queda mal (ej: `imagens`), especificá `protected $table = 'nombre_correcto'` en el modelo y corregilo en la migration.

```bash
php artisan migrate
```

---

## Paso 3 — Modelo API

```bash
php artisan make:model Api/NombreEntidad
```

Va en `app/Models/Api/` y **extiende el modelo principal**:

```php
<?php

namespace App\Models\Api;

class Negocio extends \App\Models\Negocio
{
    public function getRouteKeyName()
    {
        return 'id';
    }
}
```

---

## Paso 4 — Request

```bash
php artisan make:request NombreEntidadRequest
```

- `authorize()` devuelve `true`
- `rules()` define las validaciones de cada campo
- Para imágenes usar `mimes` en lugar de `image` para aceptar SVG:

```php
public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'nombre'  => ['required', 'string', 'max:100'],
        'activo'  => ['required', 'boolean'],
        'logo'    => ['nullable', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
        'imagenes'   => ['nullable', 'array'],
        'imagenes.*' => ['mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
    ];
}
```

---

## Paso 5 — Resources

```bash
php artisan make:resource NombreEntidadResource
php artisan make:resource NombreEntidadListResource
```

**ListResource** — para la tabla (campos mínimos):
```php
public function toArray(Request $request): array
{
    return [
        'id'         => $this->id,
        'nombre'     => $this->nombre,
        'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
    ];
}
```

**Resource** — para el formulario de edición (todos los campos):
```php
public function toArray(Request $request): array
{
    return [
        'id'      => $this->id,
        'nombre'  => $this->nombre,
        'activo'  => (bool) $this->activo,
        'logo_url' => $this->logo ? asset('storage/' . $this->logo) : null,
        'imagenes' => $this->imagenes?->map(fn($img) => [
            'id'    => $img->id,
            'url'   => asset('storage/' . $img->ruta),
            'orden' => $img->orden,
        ]),
        // ...
    ];
}
```

> **Importante:** Los campos booleanos siempre casteados con `(bool)` para que el checkbox de Vue funcione correctamente.

---

## Paso 6 — Controller API

```bash
php artisan make:controller Api/NombreEntidadController
```

### Con manejo de imágenes:

```php
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
        $perPage       = request('per_page', 10);
        $search        = request('search', '');
        $sortField     = request('sort_field', 'created_at');
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

        if ($request->hasFile('logo')) {
            if ($negocio->logo) {
                Storage::disk('public')->delete($negocio->logo);
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
        if ($negocio->logo) {
            Storage::disk('public')->delete($negocio->logo);
        }
        foreach ($negocio->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->ruta);
        }
        $negocio->delete();
        return response()->noContent();
    }

    private function saveLogo(UploadedFile $file): array
    {
        $path     = 'negocios/logos/' . Str::random(10);
        $filename = $file->getClientOriginalName();
        Storage::disk('public')->makeDirectory($path);
        Storage::disk('public')->putFileAs($path, $file, $filename);

        return [
            'logo'      => $path . '/' . $filename,
            'logo_mime' => $file->getClientMimeType(),
            'logo_size' => $file->getSize(),
        ];
    }

    private function saveImagenes(Negocio $negocio, array $files): void
    {
        foreach ($files as $index => $file) {
            $path     = 'negocios/' . $negocio->id . '/imagenes/' . Str::random(10);
            $filename = $file->getClientOriginalName();
            Storage::disk('public')->makeDirectory($path);
            Storage::disk('public')->putFileAs($path, $file, $filename);

            NegocioImagen::create([
                'negocio_id' => $negocio->id,
                'ruta'       => $path . '/' . $filename,
                'mime'       => $file->getClientMimeType(),
                'size'       => $file->getSize(),
                'orden'      => $index,
            ]);
        }
    }
}
```

> **Importante:** Usar siempre `Storage::disk('public')` en lugar de `Storage::` para evitar errores 403 al servir archivos.

---

## Paso 7 — Controller Web (Inertia)

En `app/Http/Controllers/NombreEntidadController.php`:

```php
<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\NombreEntidad;
use Illuminate\Http\Request;

class NombreEntidadController extends Controller
{
    public function index()
    {
        return Inertia::render('NombreEntidad/Index');
    }

    public function create()
    {
        return Inertia::render('NombreEntidad/Create');
    }

    public function edit(int $id)
    {
        return Inertia::render('NombreEntidad/Edit', ['id' => $id]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([/* reglas */]);
        NombreEntidad::create($data);
        return redirect()->route('nombre-entidad.index');
    }

    public function update(Request $request, NombreEntidad $entidad)
    {
        $data = $request->validate([/* reglas */]);
        $entidad->update($data);
        return redirect()->route('nombre-entidad.index');
    }

    public function destroy(NombreEntidad $entidad)
    {
        $entidad->delete();
        return redirect()->route('nombre-entidad.index');
    }
}
```

---

## Paso 8 — Rutas

### web.php
```php
use App\Http\Controllers\NombreEntidadController;

Route::middleware('auth')->group(function () {
    Route::resource('nombre-entidad', NombreEntidadController::class);
});
```

### api.php
```php
use App\Http\Controllers\Api\NombreEntidadController;

Route::get('nombre-entidad', [NombreEntidadController::class, 'index']);
Route::get('nombre-entidad/{entidad}', [NombreEntidadController::class, 'show']);
Route::post('nombre-entidad', [NombreEntidadController::class, 'store']);
Route::put('nombre-entidad/{entidad}', [NombreEntidadController::class, 'update']);
Route::delete('nombre-entidad/{entidad}', [NombreEntidadController::class, 'destroy']);
```

> **Importante:** Si hay dos `Route::resource` con el mismo nombre (web y api), los nombres de rutas colisionan. Renombrá las rutas de la API con el prefijo `api.`:
> ```php
> Route::post('negocios', [NegocioController::class, 'store'])->name('api.negocios.store');
> ```

---

## Paso 9 — Store Pinia

En `resources/js/stores/nombre-entidad.ts`:

```typescript
import { defineStore } from 'pinia';
import axios from 'axios';

export const useNombreEntidadStore = defineStore('nombre-entidad', {
    state: () => ({
        loading: false,
        data: [] as any[],
        total: 0,
        page: 1,
        limit: 10,
        from: null as number | null,
        to: null as number | null,
        links: [] as any[],
    }),

    actions: {
        async getEntidades({ url = '/api/nombre-entidad', search = '', per_page = 10, sort_field = 'created_at', sort_direction = 'desc' } = {}) {
            this.loading = true;
            try {
                const response = await axios.get(url, {
                    params: { search, per_page, sort_field, sort_direction }
                });
                const { data, meta } = response.data;
                this.data = data;
                this.total = meta.total;
                this.page = meta.current_page;
                this.limit = meta.per_page;
                this.from = meta.from;
                this.to = meta.to;
                this.links = meta.links;
            } finally {
                this.loading = false;
            }
        },

        async getEntidad(id: number) {
            return axios.get(`/api/nombre-entidad/${id}`);
        },

        // Con soporte para imágenes (FormData)
        toFormData(data: any): FormData {
            const form = new FormData();
            for (const key in data) {
                if (data[key] === null || data[key] === undefined) continue;
                if (key === 'nuevasImagenes' && Array.isArray(data[key])) {
                    data[key].forEach((file: File) => form.append('imagenes[]', file));
                } else if (key === 'imagenes') {
                    continue; // imágenes existentes no se reenvían
                } else if (key === 'logo' && data[key] instanceof File) {
                    form.append('logo', data[key]);
                } else if (key === 'logo') {
                    continue; // URL del logo existente no se reenvía
                } else if (typeof data[key] === 'boolean') {
                    form.append(key, data[key] ? '1' : '0'); // booleanos como enteros
                } else {
                    form.append(key, data[key]);
                }
            }
            return form;
        },

        async createEntidad(data: any) {
            const form = this.toFormData(data);
            return axios.post('/api/nombre-entidad', form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },

        async updateEntidad(data: any) {
            const form = this.toFormData(data);
            form.append('_method', 'PUT');
            return axios.post(`/api/nombre-entidad/${data.id}`, form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },

        async deleteEntidad(id: number) {
            return axios.delete(`/api/nombre-entidad/${id}`);
        },
    }
});
```

> **Importante:** Con `FormData`, los booleanos se convierten a string. Siempre convertirlos a `'1'` o `'0'` antes de appendear.

---

## Paso 10 — Vistas Vue

### Index.vue
- Usa `AuthenticatedLayout`
- Header con título y botón "+ Nueva entidad" que linkea a `route('entidad.create')`
- Importa y muestra el componente `EntidadTable`

### Create.vue
- Usa `AuthenticatedLayout`
- Define el objeto vacío con los campos por defecto
- En `onSubmit` llama a `store.createEntidad(data)` y después `router.visit(route('entidad.index'))`

### Edit.vue
- Recibe `id` como prop desde Inertia
- En `onMounted` llama a `store.getEntidad(id)` y asigna `response.data.data`
- En `onSubmit` llama a `store.updateEntidad({ id, ...data })` y después `router.visit`

### EntidadForm.vue
- Componente compartido entre Create y Edit
- Recibe `:entidad` como prop
- Emite `@submit` con los datos del formulario
- Carga datos externos (categorías, etc.) via axios en `onMounted`
- Genera slug automáticamente desde el nombre
- Para imágenes: input `type="file"` con `@change` que asigna a `form.logo` o `form.nuevasImagenes`
- Muestra preview del logo existente con `logo_url`
- Muestra galería de imágenes existentes con botón de eliminar individual

### EntidadTable.vue
- Carga datos en `onMounted` via el store
- Tiene input de búsqueda con `@input`
- Headers de columnas clickeables para ordenar
- Paginación con los links del meta de Laravel
- Botones Editar (Link de Inertia) y Eliminar (con confirm)

---

## Notas importantes

- **Booleanos:** castear con `(bool)` en Resource y con `'1'/'0'` en FormData del store.
- **Relaciones `belongsTo`:** especificar foreign key y primary key explícitamente: `belongsTo(Model::class, 'foreign_key', 'id')`.
- **Nombres de tablas:** Laravel pluraliza en inglés. Siempre verificar el nombre generado y agregar `protected $table` si es incorrecto.
- **Storage:** usar `Storage::disk('public')` siempre. El link simbólico se crea con `php artisan storage:link`.
- **Validación de imágenes:** usar `mimes:jpg,jpeg,png,gif,webp,svg` en lugar de `image` para aceptar SVG.
- **Colisión de nombres de rutas:** si web y api usan el mismo resource name, renombrar las rutas de API con prefijo `api.`.
- **Submit con imágenes:** usar el store con FormData, no `router.post` de Inertia.
- **Datos del Edit:** la respuesta de la API viene en `response.data.data`, no en `response.data`.
- **Imágenes existentes vs nuevas:** en el store, `imagenes` (existentes) se skipean en FormData; `nuevasImagenes` (File[]) se appendean como `imagenes[]`.