<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Nuevo sitio de interés</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <SitioInteresForm :sitio="sitio" @submit="onSubmit" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SitioInteresForm from './SitioInteresForm.vue';
import { useSitioInteresStore } from '@/stores/sitioInteres';
import { router } from '@inertiajs/vue3';

const store = useSitioInteresStore();

const sitio = {
    nombre: '',
    slug: '',
    tipo: '',
    descripcion: '',
    direccion: '',
    ciudad: '',
    telefono: '',
    web: '',
    lat: null,
    lng: null,
    place_id: '',
    orden: 0,
    activo: true,
};

async function onSubmit(data: any) {
    await store.createSitioInteres(data);
    router.visit(route('admin.sitios-interes.index'));
}
</script>
