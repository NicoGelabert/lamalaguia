import { defineStore } from 'pinia';
import axios from 'axios';

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
        },

        async enviarMensaje(texto: string) {
            if (!texto.trim()) return;

            this.mensajes.push({ rol: 'usuario', contenido: texto });
            this.cargando = true;
            this.abierto = true;

            try {
                const response = await axios.post('/api/chat', {
                    mensaje: texto,
                    historial: this.mensajes.slice(-6), // últimos 6 mensajes de contexto
                });
                this.mensajes.push({ rol: 'agente', contenido: response.data.respuesta });
            } catch {
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
        },
    }
});