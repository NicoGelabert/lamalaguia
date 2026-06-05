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
                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                <select v-model="form.categoria_negocio_id" class="mt-1 w-full border rounded px-3 py-2 text-sm">
                    <option value="">Seleccioná una categoría</option>
                    <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                        {{ cat.nombre }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea v-model="form.descripcion" rows="3" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input v-model="form.telefono" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                    <input v-model="form.whatsapp" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Web</label>
                <input v-model="form.web" type="url" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Dirección</label>
                <input v-model="form.direccion" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                <input v-model="form.ciudad" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
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

            <div class="flex items-center gap-2">
                <input v-model="form.activo" type="checkbox" id="activo" class="rounded" />
                <label for="activo" class="text-sm font-medium text-gray-700">Activo</label>
            </div>

            <!-- Logo -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Logo</label>
                <div v-if="form.logo_url" class="mt-2 mb-2">
                    <img :src="form.logo_url" alt="Logo actual" class="h-20 w-20 object-cover rounded" />
                </div>
                <input
                    type="file"
                    accept="image/*"
                    class="mt-1 w-full text-sm"
                    @change="onLogoChange"
                />
            </div>

            <!-- Imágenes -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Imágenes del negocio</label>
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
            <Link :href="route('negocios.index')" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">
                Cancelar
            </Link>
            <button type="submit" class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700">
                Guardar
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps<{
    negocio: any;
}>();

const emit = defineEmits(['submit']);

const form = ref({ ...props.negocio });
const categorias = ref<{ id: number; nombre: string }[]>([]);

onMounted(async () => {
    const response = await axios.get('/api/categorias-negocio');
    categorias.value = response.data.data;
});

function generateSlug() {
    form.value.slug = form.value.nombre
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
}

function onLogoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) form.value.logo = file;
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