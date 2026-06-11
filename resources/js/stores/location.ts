import { defineStore } from 'pinia';

const STORAGE_KEY = 'lamalaguia_ubicacion';

interface UbicacionGuardada {
    lat: number;
    lng: number;
}

export const useLocationStore = defineStore('location', {
    state: () => ({
        lat: null as number | null,
        lng: null as number | null,
        activa: false,
        denegada: false,
        cargando: false,
    }),

    actions: {
        init() {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (!raw) return;

            try {
                const data = JSON.parse(raw) as UbicacionGuardada;
                if (typeof data.lat === 'number' && typeof data.lng === 'number') {
                    this.lat = data.lat;
                    this.lng = data.lng;
                    this.activa = true;
                }
            } catch {
                sessionStorage.removeItem(STORAGE_KEY);
            }
        },

        solicitarUbicacion(): Promise<boolean> {
            if (!navigator.geolocation) {
                return Promise.resolve(false);
            }

            this.cargando = true;

            return new Promise((resolve) => {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.lat = position.coords.latitude;
                        this.lng = position.coords.longitude;
                        this.activa = true;
                        this.denegada = false;
                        this.cargando = false;
                        sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
                            lat: this.lat,
                            lng: this.lng,
                        }));
                        resolve(true);
                    },
                    () => {
                        this.activa = false;
                        this.denegada = true;
                        this.cargando = false;
                        sessionStorage.removeItem(STORAGE_KEY);
                        resolve(false);
                    },
                    { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 }
                );
            });
        },

        desactivar() {
            this.lat = null;
            this.lng = null;
            this.activa = false;
            this.denegada = false;
            sessionStorage.removeItem(STORAGE_KEY);
        },

        getUbicacionParaApi(): { lat: number; lng: number } | null {
            if (!this.activa || this.lat === null || this.lng === null) {
                return null;
            }

            return { lat: this.lat, lng: this.lng };
        },
    },
});
