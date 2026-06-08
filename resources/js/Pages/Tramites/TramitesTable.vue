<template>
    <div>
        <div class="flex justify-between items-center mb-4">
            <input
                v-model="search"
                type="text"
                placeholder="Buscar tramite..."
                class="border rounded px-3 py-2 text-sm w-64"
                @input="onSearch"
            />
        </div>

        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('titulo')">Título</th>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('categoria')">Categoría</th>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('orden')">Orden</th>
                    <th class="px-4 py-2">Activo</th>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('updated_at')">Actualizado</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="store.loading">
                    <td colspan="6" class="text-center py-4">Cargando...</td>
                </tr>
                <tr
                    v-for="tramite in store.data"
                    :key="tramite.id"
                    class="border-b hover:bg-gray-50"
                >
                    <td class="px-4 py-2">{{ tramite.titulo }}</td>
                    <td class="px-4 py-2">{{ tramite.categoria }}</td>
                    <td class="px-4 py-2">{{ tramite.orden }}</td>
                    <td class="px-4 py-2">
                        <span :class="tramite.activo ? 'text-green-600' : 'text-red-500'">
                            {{ tramite.activo ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ tramite.updated_at }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <Link :href="route('tramites.edit', tramite.id)" class="text-blue-600 hover:underline">
                            Editar
                        </Link>
                        <button @click="onDelete(tramite.id)" class="text-red-500 hover:underline">
                            Eliminar
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="flex justify-between items-center mt-4 text-sm">
            <span>Mostrando {{ store.from }} - {{ store.to }} de {{ store.total }}</span>
            <div class="flex gap-1">
                <template v-for="link in store.links" :key="link.label">
                    <button
                        v-html="link.label"
                        :disabled="!link.url"
                        :class="[
                            'px-3 py-1 border rounded',
                            link.active ? 'bg-gray-800 text-white' : 'hover:bg-gray-100',
                            !link.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                        @click="link.url && store.getTramites({ url: link.url })"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useTramiteStore } from '@/stores/tramite';

const store = useTramiteStore();
const search = ref('');
const sortField = ref('created_at');
const sortDirection = ref('desc');

onMounted(() => {
    store.getTramites();
});

function onSearch() {
    store.getTramites({ search: search.value });
}

function sortBy(field: string) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    store.getTramites({ sort_field: sortField.value, sort_direction: sortDirection.value });
}

async function onDelete(id: number) {
    if (confirm('¿Seguro que querés eliminar este tramite?')) {
        await store.deleteTramite(id);
        store.getTramites();
    }
}
</script>