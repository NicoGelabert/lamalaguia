<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Editar Tramite</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <TramiteForm v-if="tramite" :tramite="tramite" @submit="onSubmit" />
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
import { ref, onMounted } from 'vue';

const props = defineProps<{ id: number }>();
const store = useTramiteStore();
const tramite = ref(null);

onMounted(async () => {
    const response = await store.getTramite(props.id);
    tramite.value = response.data.data;
});

async function onSubmit(data: any) {
    await store.updateTramite({ id: props.id, ...data });
    router.visit(route('admin.tramites.index'));
}
</script>