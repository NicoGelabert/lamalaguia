<template>
    <span class="chat-message-content">
        <template v-for="(part, index) in parts" :key="index">
            <Link
                v-if="part.type === 'link' && part.href && isInternalHref(part.href)"
                :href="part.href"
                class="chat-message-link"
                :class="variant === 'agente' ? 'chat-message-link-agente' : 'chat-message-link-usuario'"
            >
                {{ part.content }}
            </Link>
            <a
                v-else-if="part.type === 'link' && part.href"
                :href="part.href"
                class="chat-message-link"
                :class="variant === 'agente' ? 'chat-message-link-agente' : 'chat-message-link-usuario'"
                :target="isExternalHref(part.href) ? '_blank' : undefined"
                :rel="isExternalHref(part.href) ? 'noopener noreferrer' : undefined"
            >
                {{ part.content }}
            </a>
            <span v-else>{{ part.content }}</span>
        </template>
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { isExternalHref, isInternalHref, linkifyMessage } from '@/lib/linkifyMessage';

const props = defineProps<{
    content: string;
    variant: 'usuario' | 'agente';
}>();

const parts = computed(() => linkifyMessage(props.content));
</script>
