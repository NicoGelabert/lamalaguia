import { defineStore } from 'pinia';
import axios from 'axios';

interface Negocio {
    id: number;
    nombre: string;
    slug: string;
    descripcion: string | null;
    descripcion_corta: string | null;
    direccion: string | null;
    ciudad: string;
    telefono: string | null;
    whatsapp: string | null;
    web: string | null;
    redes_sociales: { tipo: string; url: string }[];
    lat: number | null;
    lng: number | null;
    place_id: string | null;
    activo: boolean;
    destacado: boolean;
    orden_destacado: number | null;
    categoria_negocio_id: number;
    categoria: string | null;
    logo_url: string | null;
    imagenes: { id: number; url: string; orden: number }[];
    updated_at: string;
    created_at: string;
}

interface NegocioState {
    loading: boolean;
    data: Negocio[];
    total: number;
    page: number;
    limit: number;
    from: number | null;
    to: number | null;
    links: any[];
}

export const useNegocioStore = defineStore('negocios', {
    state: (): NegocioState => ({
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
        async getNegocios({ url = '/api/negocios', search = '', per_page = 10, sort_field = 'created_at', sort_direction = 'desc' } = {}) {
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
            const allowedFields = [
                'nombre',
                'slug',
                'descripcion',
                'descripcion_corta',
                'direccion',
                'ciudad',
                'telefono',
                'whatsapp',
                'web',
                'lat',
                'lng',
                'place_id',
                'activo',
                'destacado',
                'orden_destacado',
                'categoria_negocio_id',
                'logo',
            ];

            for (const key of allowedFields) {
                if (!(key in data)) {
                    continue;
                }

                const value = data[key];

                if (value === null || value === undefined) {
                    continue;
                }

                if (key === 'logo' && value instanceof File) {
                    form.append('logo', value);
                } else if (key === 'logo') {
                    continue;
                } else if (typeof value === 'boolean') {
                    form.append(key, value ? '1' : '0');
                } else if (value === '') {
                    continue;
                } else {
                    form.append(key, value);
                }
            }

            this.appendRedesSociales(form, data.redes_sociales);

            if (Array.isArray(data.nuevasImagenes)) {
                data.nuevasImagenes.forEach((file: File) => form.append('imagenes[]', file));
            }

            return form;
        },

        appendRedesSociales(form: FormData, redes: unknown): void {
            if (!Array.isArray(redes) || redes.length === 0) {
                form.append('redes_sociales', '');

                return;
            }

            redes.forEach((red, index) => {
                if (!red?.tipo || !red?.url) {
                    return;
                }

                form.append(`redes_sociales[${index}][tipo]`, red.tipo);
                form.append(`redes_sociales[${index}][url]`, red.url);
            });
        },

        async createNegocio(negocio: any) {
            const form = this.toFormData(negocio);
            return axios.post('/api/negocios', form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },

        async updateNegocio(negocio: any) {
            const form = this.toFormData(negocio);
            return axios.post(`/api/negocios/${negocio.id}`, form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },

        async deleteNegocio(id: number) {
            return axios.delete(`/api/negocios/${id}`);
        },

        async getNegocio(id: number) {
            return axios.get(`/api/negocios/${id}`);
        },
    }
});