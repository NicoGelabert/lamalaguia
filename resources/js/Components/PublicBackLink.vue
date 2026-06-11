<template>
    <Link :href="href" class="public-back-link">
        ← {{ label }}
    </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    href: string;
    label: string;
}>();
</script>
