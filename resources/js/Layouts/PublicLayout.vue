<template>
    <div class="min-h-screen flex flex-col">

        <!-- Header -->
        <header class="site-header">
            <Link href="/" class="flex items-center">
                <ApplicationLogo class="h-8 w-auto" />
            </Link>

            <button @click="menuAbierto = !menuAbierto" class="p-2 rounded-md text-gray-600 hover:bg-dark/5 transition-colors">
                <svg v-if="!menuAbierto" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <!-- Menú móvil pantalla completa -->
        <Transition name="slide-menu">
            <div v-if="menuAbierto" class="mobile-menu">
                <button
                    type="button"
                    class="mobile-menu-close"
                    aria-label="Cerrar menú"
                    @click="menuAbierto = false"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="mobile-menu-logo">
                    <Link href="/" @click="menuAbierto = false">
                        <ApplicationLogo class="h-10 w-auto" />
                    </Link>
                </div>

                <nav class="mobile-menu-nav">
                    <Link href="/tramites" class="mobile-menu-link" @click="menuAbierto = false">Trámites</Link>
                    <Link href="/negocios" class="mobile-menu-link" @click="menuAbierto = false">Negocios</Link>
                    <Link href="/eventos" class="mobile-menu-link" @click="menuAbierto = false">Eventos</Link>
                </nav>
            </div>
        </Transition>

        <!-- Contenido -->
        <main class="flex-1">
            <slot />
        </main>

        <footer class="site-footer">
            <div class="max-w-2xl mx-auto text-center">
                <ApplicationLogoWhite class="h-8 w-auto mx-auto" />
                <p class="text-white/70 text-sm mt-2">
                    Hecho por argentinos, para argentinos en Andalucía.
                </p>
                <p class="text-white/50 text-xs mt-6">
                    © {{ new Date().getFullYear() }} La Malaguía
                </p>
            </div>
        </footer>

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
        <div class="fixed bottom-0 left-0 right-0 z-50 border-t bg-cream px-4 py-3">
            <div class="max-w-2xl mx-auto flex gap-2 items-center">
                <button
                    type="button"
                    class="location-btn shrink-0"
                    :class="{ 'location-btn-active': ubicacion.activa }"
                    :title="ubicacion.activa ? 'Ubicación activa' : 'Usar mi ubicación'"
                    :disabled="ubicacion.cargando"
                    @click="toggleUbicacion"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
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
import { ref, onMounted, watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ApplicationLogoWhite from '@/Components/ApplicationLogoWhite.vue';
import { Link } from '@inertiajs/vue3';
import { useChatStore } from '@/stores/chat';
import { useLocationStore } from '@/stores/location';

const menuAbierto = ref(false);
const chat = useChatStore();
const ubicacion = useLocationStore();
const pregunta = ref('');

onMounted(() => {
    ubicacion.init();
});

watch(menuAbierto, (abierto) => {
    document.body.style.overflow = abierto ? 'hidden' : '';
});

async function toggleUbicacion() {
    if (ubicacion.activa) {
        ubicacion.desactivar();
        return;
    }

    await ubicacion.solicitarUbicacion();
}

function enviarPregunta() {
    if (!pregunta.value.trim()) return;
    chat.enviarMensaje(pregunta.value);
    pregunta.value = '';
}
</script>