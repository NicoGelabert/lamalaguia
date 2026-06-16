<template>
    <PublicLayout>
        <section class="public-page bg-cream">
            <div class="max-w-2xl mx-auto">
                <PublicBackLink :href="route('negocios.index')" label="Volver a negocios" />

                <article class="public-detail-card">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-16 h-16 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center">
                            <img
                                v-if="negocio.logo_url"
                                :src="negocio.logo_url"
                                :alt="negocio.nombre"
                                class="w-full h-full object-cover"
                            />
                            <svg v-else class="h-8 w-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span v-if="negocio.destacado" class="public-detail-badge">Destacado</span>
                                <span v-if="negocio.categoria" class="text-gray-500 text-sm">{{ negocio.categoria }}</span>
                            </div>
                            <h1 class="font-titulo text-2xl font-bold text-dark mt-1">{{ negocio.nombre }}</h1>
                            <p class="text-gray-500 text-sm mt-1">
                                {{ negocio.ciudad }}
                                <span v-if="negocio.distancia_km" class="text-primary"> · {{ negocio.distancia_km }} km</span>
                            </p>
                        </div>
                    </div>

                    <p v-if="negocio.descripcion_corta" class="text-gray-600 text-sm mt-4">
                        {{ negocio.descripcion_corta }}
                    </p>

                    <p v-if="negocio.descripcion" class="text-gray-700 text-sm mt-4 whitespace-pre-wrap leading-relaxed">
                        {{ negocio.descripcion }}
                    </p>

                    <div v-if="tieneContacto" class="public-detail-meta mt-6 pt-6 border-t border-gray-100">
                        <p v-if="negocio.direccion">
                            <span class="text-gray-400">Dirección:</span> {{ negocio.direccion }}
                        </p>
                        <p v-if="negocio.telefono">
                            <span class="text-gray-400">Teléfono: </span>
                            <a :href="`tel:${negocio.telefono}`">{{ negocio.telefono }}</a>
                        </p>
                        <p v-if="negocio.whatsapp">
                            <span class="text-gray-400">WhatsApp: </span>
                            <a :href="whatsappUrl(negocio.whatsapp)" target="_blank" rel="noopener">
                                {{ negocio.whatsapp }}
                            </a>
                        </p>
                        <p v-if="negocio.web">
                            <span class="text-gray-400">Web:</span>
                            <a :href="negocio.web" target="_blank" rel="noopener">{{ negocio.web }}</a>
                        </p>
                    </div>

                    <div v-if="negocio.redes_sociales.length" class="public-detail-meta mt-4">
                        <p class="text-gray-400 mb-2">Redes sociales</p>
                        <div class="flex flex-wrap gap-2">
                            <a
                                v-for="(red, index) in negocio.redes_sociales"
                                :key="`${red.tipo}-${index}`"
                                :href="red.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="public-social-link"
                            >
                                {{ etiquetaRedSocial(red.tipo) }}
                            </a>
                        </div>
                    </div>

                    <div v-if="negocio.lat && negocio.lng" class="mt-6">
                        <MapaNegocio
                            :lat="negocio.lat"
                            :lng="negocio.lng"
                            :nombre="negocio.nombre"
                            :place-id="negocio.place_id"
                        />
                    </div>

                    <div v-if="negocio.imagenes.length" class="public-gallery">
                        <img
                            v-for="imagen in negocio.imagenes"
                            :key="imagen.id"
                            :src="imagen.url"
                            :alt="negocio.nombre"
                        />
                    </div>

                    <div class="public-detail-cta">
                        <button type="button" class="btn-primary" @click="consultar">
                            Preguntarle a La Malaguía
                        </button>
                        <a
                            v-if="negocio.whatsapp"
                            :href="whatsappUrl(negocio.whatsapp)"
                            target="_blank"
                            rel="noopener"
                            class="btn-outline text-center"
                            @click="trackWhatsapp"
                        >
                            WhatsApp
                        </a>
                    </div>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PublicBackLink from '@/Components/PublicBackLink.vue';
import MapaNegocio from '@/Components/MapaNegocio.vue';
import { trackEvent } from '@/lib/analytics';
import { etiquetaRedSocial, type RedSocialLink } from '@/lib/redesSociales';
import { useChatStore } from '@/stores/chat';
import { useLocationStore } from '@/stores/location';

interface Imagen {
    id: number;
    url: string;
}

interface NegocioDetail {
    id: number;
    slug: string;
    nombre: string;
    ciudad: string | null;
    direccion: string | null;
    categoria: string | null;
    descripcion: string | null;
    descripcion_corta: string | null;
    telefono: string | null;
    whatsapp: string | null;
    web: string | null;
    redes_sociales: RedSocialLink[];
    lat: number | null;
    lng: number | null;
    place_id: string | null;
    destacado: boolean;
    logo_url: string | null;
    imagenes: Imagen[];
    distancia_km: number | null;
}

const props = defineProps<{
    negocio: NegocioDetail;
}>();

const chat = useChatStore();
const ubicacion = useLocationStore();

const tieneContacto = computed(() =>
    props.negocio.direccion
    || props.negocio.telefono
    || props.negocio.whatsapp
    || props.negocio.web
    || props.negocio.redes_sociales.length > 0
);

function whatsappUrl(numero: string) {
    return `https://wa.me/${numero.replace(/\D/g, '')}`;
}

function recargarConUbicacion() {
    const params: Record<string, number> = {};

    if (ubicacion.activa && ubicacion.lat !== null && ubicacion.lng !== null) {
        params.lat = ubicacion.lat;
        params.lng = ubicacion.lng;
    }

    router.get(route('negocios.show', props.negocio.slug), params, {
        preserveScroll: true,
        preserveState: true,
        only: ['negocio'],
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

function trackWhatsapp() {
    trackEvent('negocio_whatsapp_click', {
        slug: props.negocio.slug,
        destacado: props.negocio.destacado,
    });
}

function consultar() {
    trackEvent('negocio_consulta_chat', {
        slug: props.negocio.slug,
        destacado: props.negocio.destacado,
    });
    chat.enviarMensaje(`Contame más sobre ${props.negocio.nombre}`);
}
</script>
