<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Sitios de interés</h2>
                <Link :href="route('admin.sitios-interes.create')" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                    + Nuevo sitio
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <SitiosInteresTable />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import SitiosInteresTable from './SitiosInteresTable.vue';
</script>
