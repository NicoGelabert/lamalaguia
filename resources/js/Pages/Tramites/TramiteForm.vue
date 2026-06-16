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
                <div class="mt-1 border rounded">
                    <!-- Barra de herramientas -->
                    <div class="flex gap-1 p-2 border-b bg-gray-50 flex-wrap">
                        <button type="button" @click="editor?.chain().focus().toggleBold().run()" :class="editor?.isActive('bold') ? 'bg-gray-300' : ''" class="px-2 py-1 text-sm rounded hover:bg-gray-200 font-bold">B</button>
                        <button type="button" @click="editor?.chain().focus().toggleItalic().run()" :class="editor?.isActive('italic') ? 'bg-gray-300' : ''" class="px-2 py-1 text-sm rounded hover:bg-gray-200 italic">I</button>
                        <button type="button" @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()" :class="editor?.isActive('heading', { level: 2 }) ? 'bg-gray-300' : ''" class="px-2 py-1 text-sm rounded hover:bg-gray-200">H2</button>
                        <button type="button" @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()" :class="editor?.isActive('heading', { level: 3 }) ? 'bg-gray-300' : ''" class="px-2 py-1 text-sm rounded hover:bg-gray-200">H3</button>
                        <button type="button" @click="editor?.chain().focus().toggleBulletList().run()" :class="editor?.isActive('bulletList') ? 'bg-gray-300' : ''" class="px-2 py-1 text-sm rounded hover:bg-gray-200">• Lista</button>
                        <button type="button" @click="editor?.chain().focus().toggleOrderedList().run()" :class="editor?.isActive('orderedList') ? 'bg-gray-300' : ''" class="px-2 py-1 text-sm rounded hover:bg-gray-200">1. Lista</button>
                        <button type="button" @click="addImage" class="px-2 py-1 text-sm rounded hover:bg-gray-200">🖼 Imagen</button>
                    </div>
                    <!-- Editor -->
                    <EditorContent :editor="editor" class="p-3 min-h-48 prose max-w-none" />
                </div>
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
            <Link :href="route('admin.tramites.index')" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">
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
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';

const props = defineProps<{
    tramite: any;
}>();

const categorias = [
    { value: 'Documentación', label: 'Documentación' },
    { value: 'Salud', label: 'Salud' },
    { value: 'Trabajo', label: 'Trabajo' },
    { value: 'Vivienda', label: 'Vivienda' },
    { value: 'Educación', label: 'Educación' },
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

const editor = useEditor({
    content: form.value.contenido,
    extensions: [
        StarterKit,
        Image,
    ],
    onUpdate: ({ editor }) => {
        form.value.contenido = editor.getHTML();
    },
});

function addImage() {
    const url = window.prompt('URL de la imagen');
    if (url) {
        editor.value?.chain().focus().setImage({ src: url }).run();
    }
}

</script>