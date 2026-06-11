<template>
    <PublicLayout>
        <section class="public-page bg-cream">
            <div class="max-w-2xl mx-auto">
                <PublicBackLink :href="route('eventos.index')" label="Volver a eventos" />

                <article class="public-detail-card">
                    <div v-if="evento.imagen_url" class="mb-6 -mx-2 sm:mx-0">
                        <img
                            :src="evento.imagen_url"
                            :alt="evento.nombre"
                            class="w-full h-48 sm:h-56 object-cover rounded-xl"
                        />
                    </div>

                    <h1 class="font-titulo text-2xl font-bold text-dark">{{ evento.nombre }}</h1>

                    <div class="public-detail-meta mt-4">
                        <p>
                            <span class="text-gray-400">Fecha:</span> {{ evento.fecha }}
                        </p>
                        <p>
                            <span class="text-gray-400">Lugar:</span> {{ evento.lugar }}
                        </p>
                    </div>

                    <p v-if="evento.descripcion" class="text-gray-700 text-sm mt-6 whitespace-pre-wrap leading-relaxed">
                        {{ evento.descripcion }}
                    </p>

                    <div v-if="evento.lat && evento.lng" class="mt-6">
                        <MapaNegocio
                            :lat="evento.lat"
                            :lng="evento.lng"
                            :nombre="evento.nombre"
                            :place-id="evento.place_id"
                        />
                    </div>

                    <div v-if="evento.imagenes.length" class="public-gallery">
                        <img
                            v-for="imagen in evento.imagenes"
                            :key="imagen.id"
                            :src="imagen.url"
                            :alt="evento.nombre"
                        />
                    </div>

                    <div class="public-detail-cta">
                        <button type="button" class="btn-primary" @click="consultar">
                            Preguntarle a La Malaguía
                        </button>
                        <a
                            v-if="evento.url_externo"
                            :href="evento.url_externo"
                            target="_blank"
                            rel="noopener"
                            class="btn-outline text-center"
                        >
                            Más información
                        </a>
                    </div>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PublicBackLink from '@/Components/PublicBackLink.vue';
import MapaNegocio from '@/Components/MapaNegocio.vue';
import { useChatStore } from '@/stores/chat';

interface Imagen {
    id: number;
    url: string;
}

interface EventoDetail {
    id: number;
    slug: string;
    nombre: string;
    fecha: string;
    lugar: string;
    descripcion: string | null;
    url_externo: string | null;
    lat: number | null;
    lng: number | null;
    place_id: string | null;
    imagen_url: string | null;
    imagenes: Imagen[];
}

const props = defineProps<{
    evento: EventoDetail;
}>();

const chat = useChatStore();

function consultar() {
    chat.enviarMensaje(`Contame más sobre el evento "${props.evento.nombre}"`);
}
</script>
