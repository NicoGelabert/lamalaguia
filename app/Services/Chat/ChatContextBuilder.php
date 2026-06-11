<?php

namespace App\Services\Chat;

use App\Models\Evento;
use App\Models\Negocio;
use App\Models\SitioInteres;
use App\Models\Tramite;

class ChatContextBuilder
{
    private const MAX_RESULTS = 5;

    private const MAX_TEXT_LENGTH = 300;

    private const RADIUS_KM = 25;

    private const RADIUS_KM_EXPANDED = 50;

    private ?float $userLat = null;

    private ?float $userLng = null;

    private const STOP_WORDS = [
        'el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'de', 'del', 'al', 'a', 'en', 'y', 'o',
        'que', 'como', 'cómo', 'por', 'para', 'con', 'sin', 'sobre', 'es', 'son', 'está', 'esta',
        'hay', 'me', 'te', 'se', 'lo', 'le', 'mi', 'tu', 'su', 'nos', 'vos', 'les', 'más', 'mas',
        'muy', 'ya', 'si', 'sí', 'no', 'ni', 'pero', 'también', 'tambien', 'todo', 'toda', 'todos',
        'todas', 'este', 'esta', 'estos', 'estas', 'ese', 'esa', 'esos', 'esas', 'aquel', 'aquella',
        'qué', 'que', 'cuál', 'cual', 'cuáles', 'cuales', 'dónde', 'donde', 'cuándo', 'cuando',
        'quién', 'quien', 'algo', 'algún', 'algun', 'alguna', 'necesito', 'quiero', 'busco',
        'buscando', 'mostrá', 'mostra', 'mostrar', 'decime', 'contame', 'ayuda', 'ayudar', 'compro',
        'hola', 'buenas', 'gracias', 'porfa', 'favor', 'malaga', 'málaga', 'andalucia', 'andalucía',
        'españa', 'espana', 'argentino', 'argentina', 'argentinos', 'argentinas', 'comunidad',
    ];

    private const INTENT_KEYWORDS = [
        'eventos' => [
            'evento', 'eventos', 'actividad', 'actividades', 'fiesta', 'fiestas', 'agenda',
            'finde', 'fin de semana', 'reunion', 'reunión', 'asado', 'encuentro', 'encuentros',
        ],
        'negocios' => [
            'negocio', 'negocios', 'profesional', 'profesionales', 'servicio', 'servicios',
            'restaurante', 'medico', 'médico', 'contador', 'abogado', 'psicologo', 'psicólogo',
            'comprar', 'compro', 'recomend', 'tienda', 'local', 'empresa', 'contacto', 'telefono', 'teléfono',
            'whatsapp', 'direccion', 'dirección', 'empanada', 'empanadas', 'comida', 'cafeteria', 'cafetería',
        ],
        'tramites' => [
            'tramite', 'trámite', 'tramites', 'trámites', 'nie', 'empadron', 'residencia', 'visa',
            'papeles', 'gestoria', 'gestoría', 'extranjeria', 'extranjería', 'homolog', 'dni',
            'pasaporte', 'certificado', 'registro', 'cita', 'solicitud',
        ],
    ];

    public function build(string $mensaje, array $historial = [], ?array $ubicacion = null): string
    {
        $this->setUserLocation($ubicacion);

        $textoCompleto = $this->combineText($mensaje, $historial);
        $intents = $this->detectIntents($textoCompleto);
        $terms = $this->extractSearchTerms($mensaje, $historial);
        $locationTerms = $this->extractLocationTerms($mensaje, $historial);
        $contentTerms = $this->excludeLocationTerms($terms, $locationTerms);

        $sections = [];

        $searchAll = $intents === [] && ($contentTerms !== [] || $locationTerms !== []);
        $searchEventos = $searchAll || in_array('eventos', $intents, true);
        $searchNegocios = $searchAll || in_array('negocios', $intents, true);
        $searchTramites = $searchAll || in_array('tramites', $intents, true);

        if ($searchEventos) {
            $section = $this->buildEventosSection(
                $contentTerms !== [] ? $contentTerms : $terms,
                $contentTerms === [] && $terms === [] && in_array('eventos', $intents, true)
            );
            if ($section !== '') {
                $sections[] = $section;
            }
        }

        if ($searchNegocios) {
            $section = $this->buildNegociosSection(
                $contentTerms,
                $locationTerms,
                $contentTerms === [] && $locationTerms === [] && in_array('negocios', $intents, true)
            );
            if ($section !== '') {
                $sections[] = $section;
            }
        }

        if ($searchTramites) {
            $section = $this->buildTramitesSection(
                $contentTerms !== [] ? $contentTerms : $terms,
                $contentTerms === [] && $terms === [] && in_array('tramites', $intents, true)
            );
            if ($section !== '') {
                $sections[] = $section;
            }
        }

        if ($this->shouldSearchSitios($intents, $textoCompleto) && ($locationTerms !== [] || $this->hasUserLocation())) {
            $section = $this->buildSitiosInteresSection(
                $locationTerms,
                in_array('tramites', $intents, true)
            );
            if ($section !== '') {
                $sections[] = $section;
            }
        }

        if ($sections === []) {
            return '';
        }

        return "DATOS DE LA GUÍA (usá solo esta información para respuestas concretas):\n\n"
            . implode("\n\n", $sections);
    }

    private function combineText(string $mensaje, array $historial): string
    {
        $partes = [$mensaje];

        foreach (array_slice($historial, -4) as $msg) {
            if (($msg['rol'] ?? '') === 'usuario' && ! empty($msg['contenido'])) {
                $partes[] = $msg['contenido'];
            }
        }

        return strtolower(implode(' ', $partes));
    }

    private function detectIntents(string $texto): array
    {
        $intents = [];

        foreach (self::INTENT_KEYWORDS as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($texto, $keyword)) {
                    $intents[] = $intent;
                    break;
                }
            }
        }

        return array_values(array_unique($intents));
    }

    private function extractSearchTerms(string $mensaje, array $historial): array
    {
        $texto = $mensaje;

        foreach (array_slice($historial, -2) as $msg) {
            if (($msg['rol'] ?? '') === 'usuario' && ! empty($msg['contenido'])) {
                $texto .= ' ' . $msg['contenido'];
            }
        }

        $texto = mb_strtolower($texto);
        $texto = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $texto) ?? $texto;
        $words = preg_split('/\s+/', trim($texto)) ?: [];

        $terms = [];
        foreach ($words as $word) {
            if (mb_strlen($word) < 3 || in_array($word, self::STOP_WORDS, true)) {
                continue;
            }
            $terms[] = $word;
        }

        return array_values(array_unique($terms));
    }

    private function extractLocationTerms(string $mensaje, array $historial): array
    {
        $texto = $this->normalize($mensaje);

        foreach (array_slice($historial, -2) as $msg) {
            if (($msg['rol'] ?? '') === 'usuario' && ! empty($msg['contenido'])) {
                $texto .= ' ' . $this->normalize($msg['contenido']);
            }
        }

        $ciudades = array_merge(
            Negocio::query()
                ->where('activo', true)
                ->whereNotNull('ciudad')
                ->where('ciudad', '!=', '')
                ->distinct()
                ->pluck('ciudad')
                ->all(),
            SitioInteres::query()
                ->where('activo', true)
                ->whereNotNull('ciudad')
                ->where('ciudad', '!=', '')
                ->distinct()
                ->pluck('ciudad')
                ->all()
        );

        $knownCities = [
            'benalmadena', 'fuengirola', 'malaga', 'marbella', 'torremolinos', 'estepona',
            'mijas', 'ronda', 'antequera', 'velez', 'nerja', 'granada', 'sevilla', 'cadiz',
        ];

        $found = [];
        foreach (array_unique(array_merge($ciudades, $knownCities)) as $city) {
            $normalizedCity = $this->normalize($city);
            if ($normalizedCity !== '' && str_contains($texto, $normalizedCity)) {
                $found[] = $city;
            }
        }

        return array_values(array_unique($found));
    }

    private function excludeLocationTerms(array $terms, array $locationTerms): array
    {
        $normalizedLocations = array_map(fn (string $location) => $this->normalize($location), $locationTerms);

        return array_values(array_filter(
            $terms,
            fn (string $term) => ! in_array($this->normalize($term), $normalizedLocations, true)
        ));
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);

        return str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $text
        );
    }

    private function buildEventosSection(array $terms, bool $broadSearch): string
    {
        $query = Evento::query()
            ->where('activo', true)
            ->where('fecha', '>=', now());

        if ($terms !== []) {
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('nombre', 'like', "%{$term}%")
                        ->orWhere('descripcion', 'like', "%{$term}%")
                        ->orWhere('lugar', 'like', "%{$term}%");
                }
            });
        } elseif (! $broadSearch) {
            return '';
        }

        $eventos = $query->orderBy('fecha')->take(self::MAX_RESULTS * 2)->get();
        $eventos = $this->applyGeoSorting($eventos)->take(self::MAX_RESULTS);

        if ($eventos->isEmpty()) {
            return '';
        }

        $header = $this->hasUserLocation() ? 'EVENTOS (ordenados por cercanía y fecha):' : 'EVENTOS:';
        $lines = [$header];
        foreach ($eventos as $evento) {
            $line = "- {$evento->nombre} | {$evento->fecha->format('d/m/Y H:i')} | {$evento->lugar}";
            if ($evento->descripcion) {
                $line .= ' | ' . $this->truncate($evento->descripcion);
            }
            if ($evento->url_externo) {
                $line .= " | Más info: {$evento->url_externo}";
            }
            if (isset($evento->distancia_km)) {
                $line .= " | {$evento->distancia_km} km";
            } elseif ($evento->lat && $evento->lng) {
                $line .= " | Coordenadas: {$evento->lat}, {$evento->lng}";
            }
            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    private function buildNegociosSection(array $contentTerms, array $locationTerms, bool $broadSearch): string
    {
        $lines = [];
        $local = collect();

        if ($locationTerms !== []) {
            $local = $this->applyGeoSorting($this->queryNegocios($contentTerms, $locationTerms));

            if ($local->isNotEmpty()) {
                $lines[] = 'NEGOCIOS EN ' . mb_strtoupper($locationTerms[0]) . ':';
                foreach ($local as $negocio) {
                    $lines[] = $this->formatNegocio($negocio);
                }
            }
        } elseif ($this->hasUserLocation() && ($contentTerms !== [] || $broadSearch)) {
            $nearby = $this->queryNegociosNearUser($contentTerms, $broadSearch);

            if ($nearby->isNotEmpty()) {
                $ampliada = $nearby->contains(fn ($negocio) => ! empty($negocio->busqueda_ampliada));
                $lines[] = $ampliada
                    ? 'NEGOCIOS CERCA DE VOS (búsqueda ampliada):'
                    : 'NEGOCIOS CERCA DE VOS:';

                foreach ($nearby as $negocio) {
                    $lines[] = $this->formatNegocio($negocio);
                }
            }
        }

        if ($lines === [] && $contentTerms !== [] && $locationTerms !== []) {
            $alternatives = $this->applyGeoSorting(
                $this->queryNegocios($contentTerms, [], $locationTerms, $local->pluck('id')->all())
            );

            if ($alternatives->isNotEmpty()) {
                $lines[] = 'SIN RESULTADOS EN ' . mb_strtoupper($locationTerms[0]) . '. ALTERNATIVAS EN LA GUÍA:';
                foreach ($alternatives as $negocio) {
                    $lines[] = $this->formatNegocio($negocio);
                }
            }
        } elseif ($lines === [] && $contentTerms !== [] && $locationTerms === []) {
            $alternatives = $this->applyGeoSorting($this->queryNegocios($contentTerms, [], [], []));

            if ($alternatives->isNotEmpty()) {
                $lines[] = 'NEGOCIOS Y PROFESIONALES:';
                foreach ($alternatives as $negocio) {
                    $lines[] = $this->formatNegocio($negocio);
                }
            }
        } elseif ($lines !== [] && $contentTerms !== [] && $locationTerms !== []) {
            $alternatives = $this->applyGeoSorting(
                $this->queryNegocios($contentTerms, [], $locationTerms, $local->pluck('id')->all())
            );

            if ($alternatives->isNotEmpty()) {
                $lines[] = 'OTRAS ALTERNATIVAS EN LA GUÍA:';
                foreach ($alternatives as $negocio) {
                    $lines[] = $this->formatNegocio($negocio);
                }
            }
        }

        if ($lines !== []) {
            return implode("\n", $lines);
        }

        if (! $broadSearch) {
            return '';
        }

        $negocios = $this->hasUserLocation()
            ? $this->queryNegociosNearUser([], true)
            : $this->applyGeoSorting($this->queryNegocios([], [], [], []))->take(self::MAX_RESULTS);

        if ($negocios->isEmpty()) {
            return '';
        }

        $lines[] = $this->hasUserLocation() ? 'NEGOCIOS CERCA DE VOS:' : 'NEGOCIOS Y PROFESIONALES:';
        foreach ($negocios as $negocio) {
            $lines[] = $this->formatNegocio($negocio);
        }

        return implode("\n", $lines);
    }

    private function queryNegocios(
        array $contentTerms,
        array $locationTerms = [],
        array $excludeLocations = [],
        array $excludeIds = [],
        int $limit = self::MAX_RESULTS
    ) {
        $query = Negocio::query()
            ->where('activo', true)
            ->with('categoria');

        if ($contentTerms !== []) {
            $query->where(function ($q) use ($contentTerms) {
                foreach ($contentTerms as $term) {
                    $q->orWhere('nombre', 'like', "%{$term}%")
                        ->orWhere('descripcion', 'like', "%{$term}%")
                        ->orWhere('descripcion_corta', 'like', "%{$term}%")
                        ->orWhereHas('categoria', function ($cq) use ($term) {
                            $cq->where('nombre', 'like', "%{$term}%");
                        });
                }
            });
        }

        if ($locationTerms !== []) {
            $query->where(function ($q) use ($locationTerms) {
                foreach ($locationTerms as $location) {
                    $q->orWhere('ciudad', 'like', "%{$location}%")
                        ->orWhere('direccion', 'like', "%{$location}%");
                }
            });
        }

        if ($excludeIds !== []) {
            $query->whereNotIn('id', $excludeIds);
        }

        $negocios = $query->take($limit * 2)->get();

        if ($excludeLocations !== []) {
            $normalizedExclude = array_map(fn (string $location) => $this->normalize($location), $excludeLocations);

            $negocios = $negocios->filter(function (Negocio $negocio) use ($normalizedExclude) {
                $ciudad = $this->normalize($negocio->ciudad ?? '');
                $direccion = $this->normalize($negocio->direccion ?? '');

                foreach ($normalizedExclude as $location) {
                    if (
                        ($ciudad !== '' && str_contains($ciudad, $location))
                        || ($direccion !== '' && str_contains($direccion, $location))
                    ) {
                        return false;
                    }
                }

                return true;
            });
        }

        $negocios = $negocios->take($limit)->values();

        return $this->applyGeoSorting($negocios);
    }

    private function queryNegociosNearUser(array $contentTerms, bool $allowBroad): \Illuminate\Support\Collection
    {
        if (! $this->hasUserLocation()) {
            return collect();
        }

        $candidates = $this->queryNegocios(
            $contentTerms,
            [],
            [],
            [],
            self::MAX_RESULTS * 4
        );

        foreach ([self::RADIUS_KM, self::RADIUS_KM_EXPANDED] as $radius) {
            $filtered = $this->filterByRadius($candidates, $radius);

            if ($filtered->isNotEmpty()) {
                if ($radius > self::RADIUS_KM) {
                    $filtered->each(fn ($item) => $item->busqueda_ampliada = true);
                }

                return $filtered->take(self::MAX_RESULTS);
            }
        }

        if ($allowBroad && $contentTerms === []) {
            return $this->applyGeoSorting($candidates)->take(self::MAX_RESULTS);
        }

        return collect();
    }

    private function formatNegocio(Negocio $negocio): string
    {
        $categoria = $negocio->categoria?->nombre ?? 'Sin categoría';
        $ciudad = $negocio->ciudad ?: 'Ciudad no especificada';
        $line = "- {$negocio->nombre} | {$ciudad} | {$categoria}";

        $descripcion = $negocio->descripcion_corta ?: $negocio->descripcion;
        if ($descripcion) {
            $line .= ' | ' . $this->truncate($descripcion);
        }

        if (isset($negocio->distancia_km)) {
            $line .= " | {$negocio->distancia_km} km";
        }

        $contacto = array_filter([
            $negocio->telefono ? "Tel: {$negocio->telefono}" : null,
            $negocio->whatsapp ? "WhatsApp: {$negocio->whatsapp}" : null,
            $negocio->direccion ? "Dir: {$negocio->direccion}" : null,
            $negocio->web ? "Web: {$negocio->web}" : null,
        ]);

        if ($contacto !== []) {
            $line .= ' | ' . implode(' | ', $contacto);
        }

        return $line;
    }

    private function buildTramitesSection(array $terms, bool $broadSearch): string
    {
        $query = Tramite::query()->where('activo', true);

        if ($terms !== []) {
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('titulo', 'like', "%{$term}%")
                        ->orWhere('contenido', 'like', "%{$term}%")
                        ->orWhere('categoria', 'like', "%{$term}%");
                }
            });
        } elseif (! $broadSearch) {
            return '';
        }

        $tramites = $query->orderBy('orden')->take(self::MAX_RESULTS)->get();

        if ($tramites->isEmpty()) {
            return '';
        }

        $lines = ['TRÁMITES:'];
        foreach ($tramites as $tramite) {
            $line = "- {$tramite->titulo} ({$tramite->categoria})";
            if ($tramite->contenido) {
                $line .= ' | ' . $this->truncate($tramite->contenido);
            }
            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    private function shouldSearchSitios(array $intents, string $texto): bool
    {
        if (in_array('tramites', $intents, true)) {
            return true;
        }

        $keywords = [
            'ayuntamiento', 'extranjeria', 'extranjería', 'registro civil', 'oficina', 'comisaria', 'comisaría',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($texto, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function buildSitiosInteresSection(array $locationTerms, bool $tramiteContext): string
    {
        if ($locationTerms === [] && $this->hasUserLocation()) {
            $sitios = SitioInteres::query()->where('activo', true)->get();
            $sitios = $this->sortSitiosForContext($sitios, $tramiteContext);
            $sitios = $this->filterByRadius($sitios, self::RADIUS_KM_EXPANDED)->take(self::MAX_RESULTS);

            if ($sitios->isEmpty()) {
                return '';
            }

            $lines = ['SITIOS DE INTERÉS CERCA DE VOS:'];
            foreach ($sitios as $sitio) {
                $lines[] = $this->formatSitioInteres($sitio);
            }

            return implode("\n", $lines);
        }

        if ($locationTerms === []) {
            return '';
        }

        $query = SitioInteres::query()->where('activo', true);

        $query->where(function ($q) use ($locationTerms) {
            foreach ($locationTerms as $location) {
                $q->orWhere('ciudad', 'like', "%{$location}%")
                    ->orWhere('direccion', 'like', "%{$location}%")
                    ->orWhere('nombre', 'like', "%{$location}%");
            }
        });

        $sitios = $this->sortSitiosForContext($query->get(), $tramiteContext);
        $sitios = $this->applyGeoSorting($sitios)->take(self::MAX_RESULTS);

        if ($sitios->isEmpty()) {
            return '';
        }

        $lines = ['SITIOS DE INTERÉS EN ' . mb_strtoupper($locationTerms[0]) . ':'];
        foreach ($sitios as $sitio) {
            $lines[] = $this->formatSitioInteres($sitio);
        }

        return implode("\n", $lines);
    }

    private function sortSitiosForContext(\Illuminate\Support\Collection $sitios, bool $tramiteContext): \Illuminate\Support\Collection
    {
        if ($tramiteContext) {
            $priority = ['ayuntamiento', 'oficina_extranjeria', 'registro_civil', 'comisaria', 'otro'];

            return $sitios->sortBy(function (SitioInteres $sitio) use ($priority) {
                $index = array_search($sitio->tipo, $priority, true);

                return $index === false ? 999 : $index;
            })->values();
        }

        return $sitios->sortBy('orden')->values();
    }

    private function formatSitioInteres(SitioInteres $sitio): string
    {
        $tipo = match ($sitio->tipo) {
            'ayuntamiento' => 'Ayuntamiento',
            'oficina_extranjeria' => 'Oficina de Extranjería',
            'registro_civil' => 'Registro Civil',
            'comisaria' => 'Comisaría',
            default => 'Otro',
        };

        $line = "- {$sitio->nombre} | {$tipo} | {$sitio->ciudad}";

        if ($sitio->descripcion) {
            $line .= ' | ' . $this->truncate($sitio->descripcion);
        }

        $contacto = array_filter([
            $sitio->direccion ? "Dir: {$sitio->direccion}" : null,
            $sitio->telefono ? "Tel: {$sitio->telefono}" : null,
            $sitio->web ? "Web: {$sitio->web}" : null,
            isset($sitio->distancia_km) ? "{$sitio->distancia_km} km" : null,
            (! isset($sitio->distancia_km) && $sitio->lat && $sitio->lng) ? "Coordenadas: {$sitio->lat}, {$sitio->lng}" : null,
        ]);

        if ($contacto !== []) {
            $line .= ' | ' . implode(' | ', $contacto);
        }

        return $line;
    }

    private function setUserLocation(?array $ubicacion): void
    {
        $this->userLat = null;
        $this->userLng = null;

        if (! is_array($ubicacion)) {
            return;
        }

        if (! isset($ubicacion['lat'], $ubicacion['lng'])) {
            return;
        }

        $lat = (float) $ubicacion['lat'];
        $lng = (float) $ubicacion['lng'];

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return;
        }

        $this->userLat = $lat;
        $this->userLng = $lng;
    }

    private function hasUserLocation(): bool
    {
        return $this->userLat !== null && $this->userLng !== null;
    }

    private function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function applyGeoSorting(\Illuminate\Support\Collection $items): \Illuminate\Support\Collection
    {
        if (! $this->hasUserLocation()) {
            return $items;
        }

        return $items
            ->map(function ($item) {
                if ($item->lat && $item->lng) {
                    $item->distancia_km = round($this->distanceKm(
                        $this->userLat,
                        $this->userLng,
                        (float) $item->lat,
                        (float) $item->lng
                    ), 1);
                } else {
                    $item->distancia_km = null;
                }

                return $item;
            })
            ->sortBy(fn ($item) => $item->distancia_km ?? 9999)
            ->values();
    }

    private function filterByRadius(\Illuminate\Support\Collection $items, float $radiusKm): \Illuminate\Support\Collection
    {
        $sorted = $this->applyGeoSorting($items);

        return $sorted->filter(fn ($item) => $item->distancia_km !== null && $item->distancia_km <= $radiusKm)->values();
    }

    private function truncate(string $text): string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', trim($text)) ?? $text;

        if (mb_strlen($text) <= self::MAX_TEXT_LENGTH) {
            return $text;
        }

        return mb_substr($text, 0, self::MAX_TEXT_LENGTH) . '...';
    }
}
