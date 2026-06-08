import { defineStore } from 'pinia';
import axios from 'axios';

interface Tramite {
    id: number;
    titulo: string;
    slug: string;
    contenido: string | null;
    categoria: string | null;
    orden: string;
    activo: boolean;
    imagen_url: string | null;
    updated_at: string;
    created_at: string;
}

interface TramiteState {
    loading: boolean;
    data: Tramite[];
    total: number;
    page: number;
    limit: number;
    from: number | null;
    to: number | null;
    links: any[];
}

export const useTramiteStore = defineStore('tramite', {
    state: (): TramiteState => ({
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
        async getTramites({ url = '/api/tramites', search = '', per_page = 10, sort_field = 'created_at', sort_direction = 'desc' } = {}) {
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
                if (key === 'imagen' && data[key] instanceof File) {
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

        async createTramite(tramite: any) {
            const form = this.toFormData(tramite);
            return axios.post('/api/tramites', form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },

        async updateTramite(tramite: any) {
            const form = this.toFormData(tramite);
            form.append('_method', 'PUT');
            return axios.post(`/api/tramites/${tramite.id}`, form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },

        async deleteTramite(id: number) {
            return axios.delete(`/api/tramties/${id}`);
        },

        async getTramite(id: number) {
            return axios.get(`/api/tramites/${id}`);
        },
    }
});