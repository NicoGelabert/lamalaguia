<template>
    <div>
        <div class="flex justify-between items-center mb-4">
            <input
                v-model="search"
                type="text"
                placeholder="Buscar sitio..."
                class="border rounded px-3 py-2 text-sm w-64"
                @input="onSearch"
            />
        </div>

        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('nombre')">Nombre</th>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('tipo')">Tipo</th>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('ciudad')">Ciudad</th>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('orden')">Orden</th>
                    <th class="px-4 py-2">Activo</th>
                    <th class="px-4 py-2 cursor-pointer" @click="sortBy('updated_at')">Actualizado</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="store.loading">
                    <td colspan="7" class="text-center py-4">Cargando...</td>
                </tr>
                <tr
                    v-for="sitio in store.data"
                    :key="sitio.id"
                    class="border-b hover:bg-gray-50"
                >
                    <td class="px-4 py-2">{{ sitio.nombre }}</td>
                    <td class="px-4 py-2">{{ tipoLabel(sitio.tipo) }}</td>
                    <td class="px-4 py-2">{{ sitio.ciudad }}</td>
                    <td class="px-4 py-2">{{ sitio.orden }}</td>
                    <td class="px-4 py-2">
                        <span :class="sitio.activo ? 'text-green-600' : 'text-red-500'">
                            {{ sitio.activo ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ sitio.updated_at }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <Link :href="route('admin.sitios-interes.edit', sitio.id)" class="text-blue-600 hover:underline">
                            Editar
                        </Link>
                        <button @click="onDelete(sitio.id)" class="text-red-500 hover:underline">
                            Eliminar
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

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
                        @click="link.url && store.getSitiosInteres({ url: link.url })"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useSitioInteresStore } from '@/stores/sitioInteres';

const store = useSitioInteresStore();
const search = ref('');
const sortField = ref('orden');
const sortDirection = ref('asc');

const tipos: Record<string, string> = {
    ayuntamiento: 'Ayuntamiento',
    oficina_extranjeria: 'Oficina de Extranjería',
    registro_civil: 'Registro Civil',
    comisaria: 'Comisaría',
    otro: 'Otro',
};

onMounted(() => {
    store.getSitiosInteres();
});

function tipoLabel(tipo: string) {
    return tipos[tipo] ?? tipo;
}

function onSearch() {
    store.getSitiosInteres({ search: search.value });
}

function sortBy(field: string) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    store.getSitiosInteres({ sort_field: sortField.value, sort_direction: sortDirection.value });
}

async function onDelete(id: number) {
    if (confirm('¿Seguro que querés eliminar este sitio?')) {
        await store.deleteSitioInteres(id);
        store.getSitiosInteres();
    }
}
</script>
