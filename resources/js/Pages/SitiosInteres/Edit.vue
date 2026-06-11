<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Editar sitio de interés</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <SitioInteresForm v-if="sitio" :sitio="sitio" @submit="onSubmit" />
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
import { ref, onMounted } from 'vue';

const props = defineProps<{ id: number }>();
const store = useSitioInteresStore();
const sitio = ref(null);

onMounted(async () => {
    const response = await store.getSitioInteres(props.id);
    sitio.value = response.data.data;
});

async function onSubmit(data: any) {
    await store.updateSitioInteres({ id: props.id, ...data });
    router.visit(route('admin.sitios-interes.index'));
}
</script>
