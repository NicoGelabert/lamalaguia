<template>
    <PublicLayout>
        <section class="public-page bg-cream">
            <div class="max-w-2xl mx-auto">
                <header class="public-page-header">
                    <h1 class="public-page-title">Negocios</h1>
                    <p class="public-page-subtitle">
                        {{ ordenadoPorCercania
                            ? 'Locales y profesionales ordenados por cercanía'
                            : 'Locales y profesionales de la comunidad argentina' }}
                    </p>
                </header>

                <div v-if="categorias.length" class="public-filters">
                    <button
                        type="button"
                        class="public-filter-chip"
                        :class="categoriaActiva === null ? 'public-filter-chip-active' : 'public-filter-chip-inactive'"
                        @click="filtrar(null)"
                    >
                        Todos
                    </button>
                    <button
                        v-for="categoria in categorias"
                        :key="categoria.id"
                        type="button"
                        class="public-filter-chip"
                        :class="categoriaActiva === categoria.id ? 'public-filter-chip-active' : 'public-filter-chip-inactive'"
                        @click="filtrar(categoria.id)"
                    >
                        {{ categoria.nombre }}
                    </button>
                </div>

                <div v-if="negocios.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Link
                        v-for="negocio in negocios"
                        :key="negocio.id"
                        :href="route('negocios.show', negocio.slug)"
                        class="home-featured-card group"
                    >
                        <span
                            v-if="negocio.destacado"
                            class="absolute top-3 right-3 text-[10px] font-semibold uppercase tracking-wide text-primary bg-primary/15 px-2 py-0.5 rounded-full"
                        >
                            Destacado
                        </span>
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-12 h-12 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center">
                                <img
                                    v-if="negocio.logo_url"
                                    :src="negocio.logo_url"
                                    :alt="negocio.nombre"
                                    class="w-full h-full object-cover"
                                />
                                <svg v-else class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="min-w-0 text-left pr-16">
                                <p class="font-titulo font-bold text-dark text-sm">{{ negocio.nombre }}</p>
                                <p class="text-gray-500 text-sm mt-1">
                                    {{ negocio.categoria }} · {{ negocio.ciudad }}
                                    <span v-if="negocio.distancia_km" class="text-primary"> · {{ negocio.distancia_km }} km</span>
                                </p>
                                <p v-if="negocio.descripcion_corta" class="text-gray-400 text-xs mt-2 line-clamp-2">
                                    {{ negocio.descripcion_corta }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>

                <p v-else class="public-empty">
                    No hay negocios en esta categoría por ahora.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useLocationStore } from '@/stores/location';

interface Categoria {
    id: number;
    nombre: string;
}

interface NegocioItem {
    id: number;
    slug: string;
    nombre: string;
    ciudad: string;
    categoria: string | null;
    descripcion_corta: string | null;
    logo_url: string | null;
    destacado: boolean;
    distancia_km: number | null;
}

const props = defineProps<{
    negocios: NegocioItem[];
    categorias: Categoria[];
    categoriaActiva: number | null;
    ordenadoPorCercania: boolean;
}>();

const ubicacion = useLocationStore();

function paramsBase(): Record<string, number> {
    const params: Record<string, number> = {};

    if (props.categoriaActiva) {
        params.categoria = props.categoriaActiva;
    }

    if (ubicacion.activa && ubicacion.lat !== null && ubicacion.lng !== null) {
        params.lat = ubicacion.lat;
        params.lng = ubicacion.lng;
    }

    return params;
}

function filtrar(categoriaId: number | null) {
    const params = paramsBase();

    if (categoriaId) {
        params.categoria = categoriaId;
    }

    router.get(route('negocios.index'), params, {
        preserveScroll: true,
        preserveState: true,
    });
}

function recargarConUbicacion() {
    router.get(route('negocios.index'), paramsBase(), {
        preserveScroll: true,
        preserveState: true,
    });
}

onMounted(() => {
    ubicacion.init();

    if (ubicacion.activa) {
        recargarConUbicacion();
    }
});

watch(
    () => [ubicacion.activa, ubicacion.lat, ubicacion.lng] as const,
    ([activa], [activaAnterior]) => {
        if (activa || activaAnterior) {
            recargarConUbicacion();
        }
    },
);
</script>
