import { defineStore } from 'pinia';
import axios from 'axios';

interface Evento {
    id: number;
    nombre: string;
    slug: string;
    descripcion: string | null;
    fecha: string;
    lugar: string | null;
    lat: number | null;
    lng: number | null;
    place_id: string | null;
    url_externo: string | null;
    activo: boolean;
    imagen_url: string | null;
    imagenes: { id: number; url: string; orden: number }[];
    updated_at: string;
    created_at: string;
}

interface EventoState {
    loading: boolean;
    data: Evento[];
    total: number;
    page: number;
    limit: number;
    from: number | null;
    to: number | null;
    links: any[];
}

export const useEventoStore = defineStore('eventos', {
    state: (): EventoState => ({
        loading: false,
        data: [],
        total: 0,
        page: 1,
        limit: 10,
        from: null,
        to: null,
        links: [],
    }),

    actions: {
        async getEventos({ url = '/api/eventos', search = '', per_page = 10, sort_field = 'created_at', sort_direction = 'desc' } = {}) {
            this.loading = true;
            try {
                const response = await axios.get(url, {
                    params: { search, per_page, sort_field, sort_direction }
                });
                const { data, meta } = response.data;
                this.data = data;
                this.total = meta.total;
                this.page = meta.current_page;
                this.limit = meta.per_page;
                this.from = meta.from;
                this.to = meta.to;
                this.links = meta.links;
            } finally {
                this.loading = false;
            }
        },
        toFormData(data: any): FormData {
            const form = new FormData();
            for (const key in data) {
                if (data[key] === null || data[key] === undefined) continue;
                if (key === 'nuevasImagenes' && Array.isArray(data[key])) {
                    data[key].forEach((file: File) => form.append('imagenes[]', file));
                } else if (key === 'imagenes') {
                    continue;
                } else if (key === 'imagen' && data[key] instanceof File) {
                    form.append('imagen', data[key]);
                } else if (key === 'imagen') {
                    continue;
                } else if (typeof data[key] === 'boolean') {
                    form.append(key, data[key] ? '1' : '0');
                } else {
                    form.append(key, data[key]);
                }
            }
            return form;
        },

        async createEvento(evento: any) {
            const form = this.toFormData(evento);
            return axios.post('/api/eventos', form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },

        async updateEvento(evento: any) {
            const form = this.toFormData(evento);
            form.append('_method', 'PUT');
            return axios.post(`/api/eventos/${evento.id}`, form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },

        async deleteEvento(id: number) {
            return axios.delete(`/api/eventos/${id}`);
        },

        async getEvento(id: number) {
            return axios.get(`/api/eventos/${id}`);
        },
    }
});