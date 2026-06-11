<template>
    <PublicLayout>
        <section class="public-page bg-cream">
            <div class="max-w-2xl mx-auto">
                <header class="public-page-header">
                    <h1 class="public-page-title">Trámites</h1>
                    <p class="public-page-subtitle">
                        Guías para los papeles que necesitás en España
                    </p>
                </header>

                <div class="public-filters">
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
                        :key="categoria"
                        type="button"
                        class="public-filter-chip"
                        :class="categoriaActiva === categoria ? 'public-filter-chip-active' : 'public-filter-chip-inactive'"
                        @click="filtrar(categoria)"
                    >
                        {{ categoria }}
                    </button>
                </div>

                <div v-if="tramites.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <Link
                        v-for="tramite in tramites"
                        :key="tramite.id"
                        :href="route('tramites.show', tramite.slug)"
                        class="home-preview-card group"
                    >
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-12 h-12 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center">
                                <img
                                    v-if="tramite.imagen_url"
                                    :src="tramite.imagen_url"
                                    :alt="tramite.titulo"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="rounded-lg bg-secondary/15 text-secondary p-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="min-w-0 text-left">
                                <p class="font-titulo font-bold text-dark text-sm">{{ tramite.titulo }}</p>
                                <p class="text-gray-500 text-sm mt-1 capitalize">{{ tramite.categoria }}</p>
                            </div>
                        </div>
                    </Link>
                </div>

                <p v-else class="public-empty">
                    No hay trámites en esta categoría por ahora.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

interface TramiteItem {
    id: number;
    slug: string;
    titulo: string;
    categoria: string;
    imagen_url: string | null;
}

defineProps<{
    tramites: TramiteItem[];
    categorias: string[];
    categoriaActiva: string | null;
}>();

function filtrar(categoria: string | null) {
    router.get(route('tramites.index'), categoria ? { categoria } : {}, {
        preserveScroll: true,
        preserveState: true,
    });
}
</script>
