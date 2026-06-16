<template>
    <PublicLayout>

        <section class="min-h-screen flex flex-col items-center justify-center px-6 pt-24 pb-16 bg-gradient-to-b from-cream via-cream to-primary/15">
            <div class="max-w-3xl mx-auto flex flex-col items-center gap-8 text-center">

                <div>
                    <h1 class="font-titulo text-4xl sm:text-5xl font-bold text-dark leading-tight">
                        Llegaste a <span class="text-primary">Málaga</span>.<br />
                        Te ayudamos a instalarte.
                    </h1>
                    <p class="text-gray-500 text-lg max-w-lg mx-auto mt-4">
                        Guía para argentinos en Málaga y Andalucía.<br />
                        Trámites, negocios y eventos — con IA que te responde al toque.
                    </p>
                </div>

                <div class="flex flex-wrap justify-center gap-2">
                    <button
                        v-for="chip in chips"
                        :key="chip.label"
                        type="button"
                        class="hero-chip"
                        @click="enviarChip(chip.query, chip.label)"
                    >
                        {{ chip.label }}
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full max-w-2xl">
                    <Link :href="route('tramites.index')" class="hero-card group">
                        <div class="flex flex-col items-start gap-3">
                            <div class="hero-card-icon bg-primary/15 text-primary group-hover:bg-primary/25 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-titulo font-bold text-dark text-base">Trámites</h2>
                                <p class="text-gray-500 text-sm mt-1">NIE, empadronamiento, papeles</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('negocios.index')" class="hero-card group">
                        <div class="flex flex-col items-start gap-3">
                            <div class="hero-card-icon bg-secondary/15 text-secondary group-hover:bg-secondary/25 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-titulo font-bold text-dark text-base">Negocios</h2>
                                <p class="text-gray-500 text-sm mt-1">Profesionales y locales argentinos</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('eventos.index')" class="hero-card group">
                        <div class="flex flex-col items-start gap-3">
                            <div class="hero-card-icon bg-primary/15 text-primary group-hover:bg-primary/25 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-titulo font-bold text-dark text-base">Eventos</h2>
                                <p class="text-gray-500 text-sm mt-1">Lo que pasa en la comunidad</p>
                            </div>
                        </div>
                    </Link>
                </div>

            </div>
        </section>

        <div class="home-stat-bar">
            <span>
                <strong class="text-dark">{{ stats.negocios }}</strong>
                {{ stats.negocios === 1 ? 'negocio' : 'negocios' }}
            </span>
            <span class="text-gray-300 hidden sm:inline">·</span>
            <span>
                <strong class="text-dark">{{ stats.tramites }}</strong>
                {{ stats.tramites === 1 ? 'trámite' : 'trámites' }}
            </span>
            <span class="text-gray-300 hidden sm:inline">·</span>
            <span>
                <strong class="text-dark">{{ stats.eventos }}</strong>
                {{ stats.eventos === 1 ? 'evento próximo' : 'eventos próximos' }}
            </span>
        </div>

        <section v-if="proximosEventos.length" class="home-section bg-cream">
            <div class="max-w-2xl mx-auto">
                <h2 class="home-section-title">Próximos en la comunidad</h2>
                <div class="flex flex-col gap-3">
                    <Link
                        v-for="evento in proximosEventos"
                        :key="evento.id"
                        :href="route('eventos.show', evento.slug)"
                        class="home-preview-card group"
                    >
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 rounded-lg bg-primary/15 text-primary p-2 group-hover:bg-primary/25 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0 text-left">
                                <p class="font-titulo font-bold text-dark text-sm">{{ evento.nombre }}</p>
                                <p class="text-gray-500 text-sm mt-1">
                                    {{ evento.fecha }} · {{ evento.lugar }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <section v-if="tramitesPopulares.length" class="home-section bg-white">
            <div class="max-w-2xl mx-auto">
                <h2 class="home-section-title">Trámites más buscados</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <Link
                        v-for="tramite in tramitesPopulares"
                        :key="tramite.id"
                        :href="route('tramites.show', tramite.slug)"
                        class="home-preview-card group"
                    >
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 rounded-lg bg-secondary/15 text-secondary p-2 group-hover:bg-secondary/25 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0 text-left">
                                <p class="font-titulo font-bold text-dark text-sm">{{ tramite.titulo }}</p>
                                <p class="text-gray-500 text-sm mt-1">{{ tramite.categoria }}</p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <section v-if="negociosDestacados.length" class="home-section bg-cream">
            <div class="max-w-2xl mx-auto">
                <h2 class="home-section-title">Negocios destacados</h2>
                <p class="text-center text-gray-500 text-sm mb-6 -mt-2">
                    {{ ordenadoPorCercania ? 'Ordenados por cercanía, del más cercano al más lejano' : 'Locales y profesionales de la comunidad' }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Link
                        v-for="negocio in negociosDestacados"
                        :key="negocio.id"
                        :href="route('negocios.show', negocio.slug)"
                        class="home-featured-card group"
                    >
                        <span class="absolute top-3 right-3 text-[10px] font-semibold uppercase tracking-wide text-primary bg-primary/15 px-2 py-0.5 rounded-full">
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
            </div>
        </section>

        <section class="home-section bg-white">
            <div class="max-w-2xl mx-auto">
                <h2 class="home-section-title">¿Cómo funciona?</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mt-2">
                    <div v-for="paso in pasos" :key="paso.numero" class="home-step">
                        <div class="home-step-number">{{ paso.numero }}</div>
                        <div>
                            <p class="font-titulo font-bold text-dark text-sm">{{ paso.titulo }}</p>
                            <p class="text-gray-500 text-sm mt-1">{{ paso.descripcion }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </PublicLayout>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { trackEvent } from '@/lib/analytics';
import { useChatStore } from '@/stores/chat';
import { useLocationStore } from '@/stores/location';

interface Stats {
    negocios: number;
    tramites: number;
    eventos: number;
}

interface EventoPreview {
    id: number;
    slug: string;
    nombre: string;
    fecha: string;
    lugar: string;
}

interface TramitePreview {
    id: number;
    slug: string;
    titulo: string;
    categoria: string;
}

interface NegocioDestacado {
    id: number;
    slug: string;
    nombre: string;
    ciudad: string;
    categoria: string | null;
    descripcion_corta: string | null;
    logo_url: string | null;
    distancia_km: number | null;
}

defineProps<{
    stats: Stats;
    proximosEventos: EventoPreview[];
    tramitesPopulares: TramitePreview[];
    negociosDestacados: NegocioDestacado[];
    ordenadoPorCercania: boolean;
}>();

const chat = useChatStore();
const ubicacion = useLocationStore();

function recargarDestacados() {
    const opciones = {
        only: ['negociosDestacados', 'ordenadoPorCercania'],
        preserveScroll: true,
        preserveState: true,
    };

    if (ubicacion.activa && ubicacion.lat !== null && ubicacion.lng !== null) {
        router.get(route('home'), { lat: ubicacion.lat, lng: ubicacion.lng }, opciones);
        return;
    }

    router.get(route('home'), {}, opciones);
}

onMounted(() => {
    ubicacion.init();

    if (ubicacion.activa) {
        recargarDestacados();
    }
});

watch(
    () => [ubicacion.activa, ubicacion.lat, ubicacion.lng] as const,
    ([activa], [activaAnterior]) => {
        if (activa || activaAnterior) {
            recargarDestacados();
        }
    },
);

const chips = [
    { label: '¿Cómo saco el NIE?', query: '¿Cómo saco el NIE?' },
    { label: '¿Dónde compro empanadas?', query: '¿Dónde compro empanadas?' },
    { label: 'Próximos eventos', query: 'Mostrá los próximos eventos de la comunidad argentina en Málaga' },
];

const pasos = [
    {
        numero: 1,
        titulo: 'Preguntá',
        descripcion: 'Escribí lo que necesitás: un trámite, un negocio, un evento.',
    },
    {
        numero: 2,
        titulo: 'Te respondemos',
        descripcion: 'La IA usa la info real de la guía, sin inventar datos.',
    },
    {
        numero: 3,
        titulo: 'Encontrá',
        descripcion: 'Negocios, trámites y eventos de la comunidad argentina.',
    },
];

function enviarChip(query: string, label: string) {
    trackEvent('hero_chip_click', { label });
    chat.enviarMensaje(query);
}

</script>
