<template>
    <PublicLayout>
        <section class="public-page bg-cream">
            <article class="max-w-2xl mx-auto legal-content">
                <PublicBackLink :href="route('home')" label="Volver al inicio" />
                <h1 class="legal-title">{{ title }}</h1>
                <p v-if="updated" class="legal-updated">Última actualización: {{ updated }}</p>
                <slot />
            </article>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PublicBackLink from '@/Components/PublicBackLink.vue';

defineProps<{
    title: string;
    updated?: string;
}>();
</script>
