<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Nuevo Tramite</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <TramiteForm :tramite="tramite" @submit="onSubmit" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TramiteForm from './TramiteForm.vue';
import { useTramiteStore } from '@/stores/tramite';
import { router } from '@inertiajs/vue3';

const store = useTramiteStore();

const tramite = {
    titulo: '',
    slug: '',
    contenido: '',
    categoria: '',
    orden: '0',
    activo: true,
};

async function onSubmit(data: any) {
    await store.createTramite(data);
    router.visit(route('tramites.index'));
}
</script>