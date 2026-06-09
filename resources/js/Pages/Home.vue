<template>
    <PublicLayout>

        <!-- Hero -->
        <section class="min-h-screen flex flex-col items-center justify-center px-6 text-center gap-12">
            <h1 class="text-4xl font-bold text-gray-900 leading-tight mb-4">
                Llegaste a Málaga.<br />Te ayudamos a instalarte.
            </h1>
            <p class="text-gray-500 text-lg max-w-md mb-10">
                Guía para argentinos en Málaga y Andalucía. Trámites, negocios y eventos de la comunidad.
            </p>

            <div class="flex flex-wrap gap-6 justify-center w-full max-w-sm">
                <button @click="iniciarNegocios" class="btn btn-primary">
                    Profesionales y negocios
                </button>
                <button @click="iniciarEventos" class="btn btn-primary">
                    Eventos
                </button>
                <button @click="iniciarTramite" class="btn btn-primary">
                    Busco ayuda con un trámite
                </button>
            </div>
        </section>

    </PublicLayout>
</template>

<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useChatStore } from '@/stores/chat';

const chat = useChatStore();

function iniciarTramite() {
    chat.iniciarConversacion(
        'Busco ayuda con un trámite',
        '¿Qué tipo de trámite tenés que hacer?'
    );
}

function iniciarNegocios() {
    chat.iniciarConversacion(
        'Profesionales y negocios',
        '¿Qué estás buscando exactamente?'
    );
}

async function iniciarEventos() {
    chat.mensajes = [];
    chat.mensajes.push({ rol: 'usuario', contenido: 'Eventos' });
    chat.cargando = true;
    chat.abierto = true;
    await chat.enviarMensaje('Mostrá los próximos eventos de la comunidad argentina en Málaga');
}
</script>