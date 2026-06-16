<template>
    <PublicLayout>
        <section class="public-page bg-cream">
            <div class="max-w-2xl mx-auto">
                <PublicBackLink :href="route('tramites.index')" label="Volver a trámites" />

                <article class="public-detail-card">
                    <div v-if="tramite.imagen_url" class="mb-6 -mx-2 sm:mx-0">
                        <img
                            :src="tramite.imagen_url"
                            :alt="tramite.titulo"
                            class="w-full h-48 sm:h-56 object-cover rounded-xl"
                        />
                    </div>

                    <span class="public-detail-badge">{{ tramite.categoria }}</span>
                    <h1 class="font-titulo text-2xl font-bold text-dark mt-3">{{ tramite.titulo }}</h1>

                    <div
                        v-if="tramite.contenido"
                        class="public-content mt-6"
                        v-html="tramite.contenido"
                    />

                    <div class="public-detail-cta">
                        <button type="button" class="btn-primary" @click="consultar">
                            ¿Tengo dudas sobre este trámite
                        </button>
                    </div>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PublicBackLink from '@/Components/PublicBackLink.vue';
import { trackEvent } from '@/lib/analytics';
import { useChatStore } from '@/stores/chat';

interface TramiteDetail {
    id: number;
    slug: string;
    titulo: string;
    categoria: string;
    contenido: string | null;
    imagen_url: string | null;
}

const props = defineProps<{
    tramite: TramiteDetail;
}>();

const chat = useChatStore();

function consultar() {
    trackEvent('tramite_consulta_chat', {
        slug: props.tramite.slug,
        categoria: props.tramite.categoria,
    });
    chat.enviarMensaje(`¿Cómo hago el trámite de ${props.tramite.titulo}?`);
}
</script>
