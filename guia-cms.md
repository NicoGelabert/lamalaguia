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
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
```

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

```php
public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'nombre' => ['required', 'string', 'max:100'],
        'activo' => ['required', 'boolean'],
        // ...
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
        'id' => $this->id,
        'nombre' => $this->nombre,
        'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
    ];
}
```

**Resource** — para el formulario de edición (todos los campos):
```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'nombre' => $this->nombre,
        'activo' => (bool) $this->activo,   // ← cast a bool para el checkbox
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

Métodos estándar:

```php
public function index()
{
    $perPage = request('per_page', 10);
    $search = request('search', '');
    $sortField = request('sort_field', 'created_at');
    $sortDirection = request('sort_direction', 'desc');

    $query = Negocio::query()
        ->with('categoria')                          // eager load relaciones
        ->where('nombre', 'like', "%{$search}%")
        ->orderBy($sortField, $sortDirection)
        ->paginate($perPage);

    return NegocioListResource::collection($query);
}

public function store(NegocioRequest $request)
{
    $data = $request->validated();
    $negocio = Negocio::create($data);
    return new NegocioResource($negocio->load('categoria'));
}

public function show(Negocio $negocio)
{
    return new NegocioResource($negocio->load('categoria'));
}

public function update(NegocioRequest $request, Negocio $negocio)
{
    $data = $request->validated();
    $negocio->update($data);
    return new NegocioResource($negocio->load('categoria'));
}

public function destroy(Negocio $negocio)
{
    $negocio->delete();
    return response()->noContent();
}
```

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

        async createEntidad(data: any) {
            return axios.post('/api/nombre-entidad', data);
        },

        async updateEntidad(data: any) {
            return axios.put(`/api/nombre-entidad/${data.id}`, data);
        },

        async deleteEntidad(id: number) {
            return axios.delete(`/api/nombre-entidad/${id}`);
        },
    }
});
```

---

## Paso 10 — Vistas Vue

### Index.vue
- Usa `AuthenticatedLayout`
- Header con título y botón "+ Nueva entidad" que linkea a `route('entidad.create')`
- Importa y muestra el componente `EntidadTable`

### Create.vue
- Usa `AuthenticatedLayout`
- Define el objeto vacío con los campos por defecto
- En `onSubmit` usa `router.post(route('entidad.store'), data)` de Inertia

### Edit.vue
- Recibe `id` como prop desde Inertia
- En `onMounted` llama a `store.getEntidad(id)` y asigna `response.data.data`
- En `onSubmit` usa `router.put(route('entidad.update', id), data)` de Inertia

### EntidadForm.vue
- Componente compartido entre Create y Edit
- Recibe `:entidad` como prop
- Emite `@submit` con los datos del formulario
- Carga datos externos (categorías, etc.) via axios en `onMounted`
- Genera slug automáticamente desde el nombre

### EntidadTable.vue
- Carga datos en `onMounted` via el store
- Tiene input de búsqueda con `@input`
- Headers de columnas clickeables para ordenar
- Paginación con los links del meta de Laravel
- Botones Editar (Link de Inertia) y Eliminar (con confirm)

---

## Notas importantes

- **Booleanos:** siempre castear con `(bool)` en el Resource para que los checkboxes funcionen en Vue.
- **Relaciones `belongsTo`:** especificar la foreign key y la primary key explícitamente para evitar problemas de resolución.
- **Nombres de tablas:** si no siguen la convención inglesa, agregar `protected $table = 'nombre_tabla'` en el modelo.
- **Slug:** generarlo automáticamente desde el nombre en el frontend, normalizando caracteres especiales y espacios.
- **Submit en Create/Edit:** usar `router.post/put` de Inertia, no axios, para que la sesión se maneje correctamente.
- **Datos del Edit:** la respuesta de la API viene en `response.data.data`, no en `response.data`.
