import { defineStore } from 'pinia';
import axios from 'axios';

interface SitioInteres {
    id: number;
    nombre: string;
    slug: string;
    tipo: string;
    descripcion: string | null;
    direccion: string | null;
    ciudad: string;
    telefono: string | null;
    web: string | null;
    lat: number | null;
    lng: number | null;
    place_id: string | null;
    orden: number;
    activo: boolean;
    updated_at: string;
    created_at: string;
}

interface SitioInteresState {
    loading: boolean;
    data: SitioInteres[];
    total: number;
    page: number;
    limit: number;
    from: number | null;
    to: number | null;
    links: any[];
}

export const useSitioInteresStore = defineStore('sitiosInteres', {
    state: (): SitioInteresState => ({
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
        async getSitiosInteres({ url = '/api/sitios-interes', search = '', per_page = 10, sort_field = 'orden', sort_direction = 'asc' } = {}) {
            this.loading = true;
            try {
                const response = await axios.get(url, {
                    params: { search, per_page, sort_field, sort_direction },
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
                if (data[key] === null || data[key] === undefined || data[key] === '') {
                    continue;
                }
                if (typeof data[key] === 'boolean') {
                    form.append(key, data[key] ? '1' : '0');
                } else {
                    form.append(key, data[key]);
                }
            }
            return form;
        },

        async createSitioInteres(sitio: any) {
            return axios.post('/api/sitios-interes', this.toFormData(sitio));
        },

        async updateSitioInteres(sitio: any) {
            const form = this.toFormData(sitio);
            form.append('_method', 'PUT');
            return axios.post(`/api/sitios-interes/${sitio.id}`, form);
        },

        async deleteSitioInteres(id: number) {
            return axios.delete(`/api/sitios-interes/${id}`);
        },

        async getSitioInteres(id: number) {
            return axios.get(`/api/sitios-interes/${id}`);
        },
    },
});
