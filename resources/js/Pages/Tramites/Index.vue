<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Tramites</h2>
                <Link :href="route('tramites.create')" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                    + Nuevo tramite
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <TramitesTable />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import TramitesTable from './TramitesTable.vue';
</script>