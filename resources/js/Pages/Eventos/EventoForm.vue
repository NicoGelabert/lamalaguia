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
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea v-model="form.descripcion" rows="3" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha y hora</label>
                <input v-model="form.fecha" type="datetime-local" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Lugar</label>
                <input v-model="form.lugar" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
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
                <label class="block text-sm font-medium text-gray-700">URL externo</label>
                <input v-model="form.url_externo" type="url" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div class="flex items-center gap-2">
                <input v-model="form.activo" type="checkbox" id="activo" class="rounded" />
                <label for="activo" class="text-sm font-medium text-gray-700">Activo</label>
            </div>

            <!-- Imagen principal -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Imagen principal</label>
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

            <!-- Galería -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Galería de imágenes</label>
                <div v-if="form.imagenes && form.imagenes.length" class="flex gap-2 mt-2 mb-2 flex-wrap">
                    <div v-for="img in form.imagenes" :key="img.id" class="relative">
                        <img :src="img.url" alt="Imagen" class="h-20 w-20 object-cover rounded" />
                        <button
                            type="button"
                            @click="removeImagen(img.id)"
                            class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center"
                        >×</button>
                    </div>
                </div>
                <input
                    type="file"
                    accept="image/*"
                    multiple
                    class="mt-1 w-full text-sm"
                    @change="onImagenesChange"
                />
            </div>

        </div>

        <div class="flex justify-end gap-3 mt-6">
            <Link :href="route('admin.eventos.index')" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">
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
    evento: any;
}>();

const emit = defineEmits(['submit']);

const form = ref({ ...props.evento });

function generateSlug() {
    form.value.slug = form.value.nombre
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

function onImagenesChange(e: Event) {
    const files = (e.target as HTMLInputElement).files;
    if (files) {
        form.value.nuevasImagenes = Array.from(files);
    }
}

function removeImagen(id: number) {
    form.value.imagenes = form.value.imagenes.filter((img: any) => img.id !== id);
}

function onSubmit() {
    emit('submit', form.value);
}
</script>