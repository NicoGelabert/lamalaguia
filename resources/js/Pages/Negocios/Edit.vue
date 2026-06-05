<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Editar Negocio</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <NegocioForm v-if="negocio" :negocio="negocio" @submit="onSubmit" />
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
import { ref, onMounted } from 'vue';

const props = defineProps<{ id: number }>();
const store = useNegocioStore();
const negocio = ref(null);

onMounted(async () => {
    const response = await store.getNegocio(props.id);
    negocio.value = response.data.data;
});

async function onSubmit(data: any) {
    await store.updateNegocio({ id: props.id, ...data });
    router.visit(route('negocios.index'));
}
</script>