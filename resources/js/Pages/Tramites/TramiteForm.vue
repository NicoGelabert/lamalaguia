<template>
    <form @submit.prevent="onSubmit">
        <div class="grid grid-cols-1 gap-4">

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input v-model="form.titulo" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" @input="generateSlug" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Slug</label>
                <input v-model="form.slug" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                <select v-model="form.categoria" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                    <option value="">Seleccioná una categoría</option>
                    <option v-for="cat in categorias" :key="cat.value" :value="cat.value">
                        {{ cat.label }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contenido</label>
                <textarea v-model="form.contenido" rows="3" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Orden</label>
                <input v-model="form.orden" type="number" min="0" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div class="flex items-center gap-2">
                <input v-model="form.activo" type="checkbox" id="activo" class="rounded" />
                <label for="activo" class="text-sm font-medium text-gray-700">Activo</label>
            </div>

            <!-- Imagen -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Imagen</label>
                <div v-if="form.imagen_url" class="mt-2 mb-2">
                    <img :src="form.imagen_url" alt="Imagen actual" class="h-20 w-20 object-cover rounded" />
                </div>
                <input
                    type="file"
                    accept="image/*"
                    class="mt-1 w-full text-sm"
                    @change="onImagenChange"
                />
            </div>

        </div>

        <div class="flex justify-end gap-3 mt-6">
            <Link :href="route('tramites.index')" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">
                Cancelar
            </Link>
            <button type="submit" class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700">
                Guardar
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { ref, } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    tramite: any;
}>();

const categorias = [
    { value: 'documentación', label: 'Documentación' },
    { value: 'salud', label: 'Salud' },
    { value: 'trabajo', label: 'Trabajo' },
    { value: 'vivienda', label: 'Vivienda' },
    { value: 'educación', label: 'Educación' },
];

const emit = defineEmits(['submit']);

const form = ref({ ...props.tramite });

function generateSlug() {
    form.value.slug = form.value.titulo
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
}

function onImagenChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.value.imagen = file;
        form.value.imagen_url = URL.createObjectURL(file);
    }
}

function onSubmit() {
    emit('submit', form.value);
}
</script>