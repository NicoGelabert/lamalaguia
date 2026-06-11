<template>
    <PublicLayout>
        <section class="public-page bg-cream">
            <div class="max-w-2xl mx-auto">
                <header class="public-page-header">
                    <h1 class="public-page-title">Eventos</h1>
                    <p class="public-page-subtitle">
                        Lo que viene en la comunidad argentina
                    </p>
                </header>

                <div v-if="eventos.length" class="flex flex-col gap-3">
                    <Link
                        v-for="evento in eventos"
                        :key="evento.id"
                        :href="route('eventos.show', evento.slug)"
                        class="home-preview-card group"
                    >
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-14 h-14 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center">
                                <img
                                    v-if="evento.imagen_url"
                                    :src="evento.imagen_url"
                                    :alt="evento.nombre"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="rounded-lg bg-primary/15 text-primary p-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="min-w-0 text-left">
                                <p class="font-titulo font-bold text-dark text-sm">{{ evento.nombre }}</p>
                                <p class="text-gray-500 text-sm mt-1">
                                    {{ evento.fecha }} · {{ evento.lugar }}
                                </p>
                                <p v-if="evento.descripcion" class="text-gray-400 text-xs mt-2 line-clamp-2">
                                    {{ evento.descripcion }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>

                <p v-else class="public-empty">
                    No hay eventos próximos por ahora. Volvé pronto.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

interface EventoItem {
    id: number;
    slug: string;
    nombre: string;
    fecha: string;
    lugar: string;
    descripcion: string | null;
    imagen_url: string | null;
}

defineProps<{
    eventos: EventoItem[];
}>();

</script>
