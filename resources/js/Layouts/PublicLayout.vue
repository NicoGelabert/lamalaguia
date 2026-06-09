<template>
    <div class="min-h-screen flex flex-col">

        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 border-b border-gray-100 px-4 h-16 flex items-center justify-between">
            <Link href="/" class="flex items-center">
                <ApplicationLogo class="h-8 w-auto" />
            </Link>

            <button @click="menuAbierto = !menuAbierto" class="p-2 rounded-md text-gray-600 hover:bg-gray-100">
                <svg v-if="!menuAbierto" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <!-- Menú hamburguesa -->
        <div v-if="menuAbierto" class="fixed top-16 left-0 right-0 z-40 bg-white border-b border-gray-100 shadow-md px-4 py-4 flex flex-col gap-3">
            <Link href="/tramites" class="text-gray-700 hover:text-gray-900 text-sm font-medium" @click="menuAbierto = false">Trámites</Link>
            <Link href="/negocios" class="text-gray-700 hover:text-gray-900 text-sm font-medium" @click="menuAbierto = false">Negocios</Link>
            <Link href="/eventos" class="text-gray-700 hover:text-gray-900 text-sm font-medium" @click="menuAbierto = false">Eventos</Link>
        </div>

        <!-- Contenido -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Chat pantalla completa -->
        <transition name="slide-up">
            <div v-if="chat.abierto" class="fixed inset-0 z-40 bg-cream flex flex-col pt-16 pb-20">
                
                <!-- Cabecera del chat -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <span class="font-titulo font-bold text-dark text-sm">La Malaguía</span>
                    <button @click="chat.abierto = false" class="text-gray-400 hover:text-dark">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mensajes -->
                <div class="flex-1 overflow-y-auto px-4 py-4 flex flex-col gap-3">
                    <div
                        v-for="(mensaje, index) in chat.mensajes"
                        :key="index"
                        :class="mensaje.rol === 'usuario' ? 'items-start' : 'items-end'"
                        class="flex flex-col"
                    >
                        <div
                            :class="mensaje.rol === 'usuario'
                                ? 'bg-white text-dark border border-gray-100 rounded-tl-2xl rounded-tr-2xl rounded-br-2xl'
                                : 'bg-dark text-white rounded-tl-2xl rounded-tr-2xl rounded-bl-2xl'"
                            class="max-w-xs px-4 py-3 text-sm shadow-sm"
                        >
                            {{ mensaje.contenido }}
                        </div>
                    </div>

                    <!-- Indicador de carga -->
                    <div v-if="chat.cargando" class="flex flex-col items-end">
                        <div class="bg-dark text-white rounded-2xl px-4 py-3 text-sm shadow-sm">
                            Escribiendo...
                        </div>
                    </div>
                </div>

            </div>
        </transition>

        <!-- Chat IA fixed bottom -->
        <div class="fixed bottom-0 left-0 right-0 z-50 border-t border-gray-100 px-4 py-3">
            <div class="max-w-2xl mx-auto flex gap-2 items-center">
                <input
                    v-model="pregunta"
                    type="text"
                    placeholder="¿En qué te puedo ayudar?"
                    class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:border-gray-500"
                    @keydown.enter="enviarPregunta"
                />
                <button @click="enviarPregunta" class="btn-send">
                    Enviar
                </button>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { useChatStore } from '@/stores/chat';

const menuAbierto = ref(false);
const chat = useChatStore();
const pregunta = ref('');

function enviarPregunta() {
    if (!pregunta.value.trim()) return;
    chat.enviarMensaje(pregunta.value);
    pregunta.value = '';
}
</script>