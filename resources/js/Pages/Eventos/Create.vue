<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Nuevo Evento</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <EventoForm :evento="evento" @submit="onSubmit" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EventoForm from './EventoForm.vue';
import { useEventoStore } from '@/stores/evento';
import { router } from '@inertiajs/vue3';

const store = useEventoStore();

const evento = {
    nombre: '',
    slug: '',
    descripcion: '',
    fecha: '',
    lugar: '',
    url_externo: '',
    activo: true,
};

async function onSubmit(data: any) {
    await store.createEvento(data);
    router.visit(route('eventos.index'));
}
</script>