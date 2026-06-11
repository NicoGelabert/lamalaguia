<template>
    <form @submit.prevent="onSubmit">
        <div class="grid grid-cols-1 gap-4">

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input v-model="form.nombre" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" @input="generateSlug" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Slug</label>
                <input v-model="form.slug" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                <select v-model="form.tipo" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                    <option value="">Seleccioná un tipo</option>
                    <option v-for="tipo in tipos" :key="tipo.value" :value="tipo.value">
                        {{ tipo.label }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea v-model="form.descripcion" rows="3" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                <input v-model="form.ciudad" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Dirección</label>
                <input v-model="form.direccion" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input v-model="form.telefono" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Web</label>
                    <input v-model="form.web" type="url" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Latitud</label>
                    <input v-model="form.lat" type="number" step="any" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Longitud</label>
                    <input v-model="form.lng" type="number" step="any" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
                </div>
            </div>

            <div v-if="form.lat && form.lng">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                <MapaNegocio
                    :lat="Number(form.lat)"
                    :lng="Number(form.lng)"
                    :nombre="form.nombre"
                    :place-id="form.place_id"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Google Place ID</label>
                <input v-model="form.place_id" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" placeholder="ChIJ..." />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Orden</label>
                <input v-model.number="form.orden" type="number" min="0" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div class="flex items-center gap-2">
                <input v-model="form.activo" type="checkbox" id="activo" class="rounded" />
                <label for="activo" class="text-sm font-medium text-gray-700">Activo</label>
            </div>

        </div>

        <div class="flex justify-end gap-3 mt-6">
            <Link :href="route('admin.sitios-interes.index')" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">
                Cancelar
            </Link>
            <button type="submit" class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700">
                Guardar
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import MapaNegocio from '@/Components/MapaNegocio.vue';

const props = defineProps<{
    sitio: any;
}>();

const emit = defineEmits(['submit']);

const form = ref({ ...props.sitio });

const tipos = [
    { value: 'ayuntamiento', label: 'Ayuntamiento' },
    { value: 'oficina_extranjeria', label: 'Oficina de Extranjería' },
    { value: 'registro_civil', label: 'Registro Civil' },
    { value: 'comisaria', label: 'Comisaría' },
    { value: 'otro', label: 'Otro' },
];

function generateSlug() {
    form.value.slug = form.value.nombre
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
}

function onSubmit() {
    emit('submit', form.value);
}
</script>
