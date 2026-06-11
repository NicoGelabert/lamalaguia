<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Nuevo Negocio</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <NegocioForm :negocio="negocio" @submit="onSubmit" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NegocioForm from './NegocioForm.vue';
import { useNegocioStore } from '@/stores/negocio';
import { router } from '@inertiajs/vue3';

const store = useNegocioStore();

const negocio = {
    nombre: '',
    slug: '',
    descripcion: '',
    descripcion_corta: '',
    direccion: '',
    ciudad: 'Málaga',
    telefono: '',
    whatsapp: '',
    web: '',
    lat: null,
    lng: null,
    place_id: '',
    activo: true,
    destacado: false,
    orden_destacado: null,
    categoria_negocio_id: null,
};

async function onSubmit(data: any) {
    await store.createNegocio(data);
    router.visit(route('admin.negocios.index'));
}
</script>