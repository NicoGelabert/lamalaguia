import { defineStore } from 'pinia';
import axios from 'axios';
import { trackEvent } from '@/lib/analytics';
import { useLocationStore } from '@/stores/location';

interface Mensaje {
    rol: 'usuario' | 'agente';
    contenido: string;
}

export const useChatStore = defineStore('chat', {
    state: () => ({
        mensajes: [] as Mensaje[],
        cargando: false,
        abierto: false,
    }),

    actions: {
        abrirChat() {
            this.abierto = true;
            trackEvent('chat_abierto');
        },

        async enviarMensaje(texto: string) {
            if (!texto.trim()) return;

            this.mensajes.push({ rol: 'usuario', contenido: texto });
            this.cargando = true;
            this.abierto = true;

            try {
                const historial = this.mensajes.slice(0, -1).slice(-4);

                const ubicacion = useLocationStore().getUbicacionParaApi();

                trackEvent('chat_mensaje_enviado', {
                    con_ubicacion: Boolean(ubicacion),
                    longitud: texto.trim().length,
                });

                const response = await axios.post('/api/chat', {
                    mensaje: texto,
                    historial,
                    ubicacion,
                });
                this.mensajes.push({ rol: 'agente', contenido: response.data.respuesta });
            } catch {
                trackEvent('chat_error');
                this.mensajes.push({ rol: 'agente', contenido: 'Hubo un error, intentá de nuevo.' });
            } finally {
                this.cargando = false;
            }
        },

        iniciarConversacion(pregunta: string, respuesta: string) {
            this.mensajes = [];
            this.mensajes.push({ rol: 'usuario', contenido: pregunta });
            this.mensajes.push({ rol: 'agente', contenido: respuesta });
            this.abierto = true;
            trackEvent('chat_conversacion_iniciada');
        },
    }
});