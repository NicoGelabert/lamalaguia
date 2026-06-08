<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;

defineProps<{
    stats: { titulo: string; total: number }[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">

                <p class="text-gray-600 mb-6">Bienvenido, {{ user.name }}!</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div
                        v-for="stat in stats"
                        :key="stat.titulo"
                        class="bg-white shadow-sm rounded-lg p-6 flex flex-col items-center"
                    >
                        <span class="text-4xl font-bold text-gray-800">{{ stat.total }}</span>
                        <span class="text-sm text-gray-500 mt-2">{{ stat.titulo }}</span>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>