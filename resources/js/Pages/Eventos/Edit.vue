<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Editar Evento</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <EventoForm v-if="evento" :evento="evento" @submit="onSubmit" />
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
import { ref, onMounted } from 'vue';

const props = defineProps<{ id: number }>();
const store = useEventoStore();
const evento = ref(null);

onMounted(async () => {
    const response = await store.getEvento(props.id);
    evento.value = response.data.data;
});

async function onSubmit(data: any) {
    await store.updateEvento({ id: props.id, ...data });
    router.visit(route('eventos.index'));
}
</script>