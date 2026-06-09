<template>
    <div ref="mapContainer" class="w-full h-64 rounded-lg z-0"></div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps<{
    lat: number;
    lng: number;
    nombre: string;
    placeId?: string | null;
}>();

const mapContainer = ref<HTMLElement | null>(null);
let map: L.Map | null = null;

onMounted(() => {
    if (!mapContainer.value) return;

    map = L.map(mapContainer.value).setView([props.lat, props.lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Ícono personalizado
    const icono = L.divIcon({
        className: '',
        html: `<div class="bg-dark text-white text-xs font-bold px-2 py-1 rounded-full shadow">${props.nombre}</div>`,
        iconAnchor: [0, 0],
    });

    const marker = L.marker([props.lat, props.lng], { icon: icono }).addTo(map);

    // Link a Google Maps si hay place_id
    if (props.placeId) {
        marker.bindPopup(`
            <div class="text-sm">
                <strong>${props.nombre}</strong><br/>
                <a href="https://www.google.com/maps/place/?q=place_id:${props.placeId}" 
                   target="_blank" 
                   class="text-blue-600 underline">
                   Ver en Google Maps
                </a>
            </div>
        `);
    }
});

onUnmounted(() => {
    map?.remove();
});
</script>