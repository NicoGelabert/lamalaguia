<template>
    <PublicLayout>
        <section class="public-page bg-cream">
            <div class="max-w-2xl mx-auto">
                <PublicBackLink :href="route('home')" label="Volver al inicio" />

                <header class="public-page-header mb-6">
                    <h1 class="public-page-title">Contacto</h1>
                    <p class="public-page-subtitle">
                        ¿Dudas, sugerencias o querés colaborar? Escribinos.
                    </p>
                </header>

                <div
                    v-if="enviado"
                    class="public-detail-card text-center"
                >
                    <p class="font-titulo font-bold text-dark">¡Gracias por escribirnos!</p>
                    <p class="text-gray-500 text-sm mt-2">
                        Recibimos tu mensaje y te responderemos a <strong>{{ form.email }}</strong> lo antes posible.
                    </p>
                    <button type="button" class="btn-outline mt-6" @click="nuevoMensaje">
                        Enviar otro mensaje
                    </button>
                </div>

                <form v-else class="public-detail-card space-y-4" @submit.prevent="enviar">
                    <div>
                        <label for="nombre" class="contact-label">Nombre</label>
                        <input
                            id="nombre"
                            v-model="form.nombre"
                            type="text"
                            required
                            maxlength="100"
                            class="contact-input"
                            autocomplete="name"
                        />
                        <p v-if="form.errors.nombre" class="contact-error">{{ form.errors.nombre }}</p>
                    </div>

                    <div>
                        <label for="email" class="contact-label">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            maxlength="150"
                            class="contact-input"
                            autocomplete="email"
                        />
                        <p v-if="form.errors.email" class="contact-error">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="asunto" class="contact-label">Asunto <span class="text-gray-400">(opcional)</span></label>
                        <input
                            id="asunto"
                            v-model="form.asunto"
                            type="text"
                            maxlength="150"
                            class="contact-input"
                            placeholder="Consulta general"
                        />
                        <p v-if="form.errors.asunto" class="contact-error">{{ form.errors.asunto }}</p>
                    </div>

                    <div>
                        <label for="mensaje" class="contact-label">Mensaje</label>
                        <textarea
                            id="mensaje"
                            v-model="form.mensaje"
                            required
                            rows="5"
                            maxlength="2000"
                            class="contact-input resize-y min-h-[120px]"
                            placeholder="Contanos en qué podemos ayudarte..."
                        />
                        <p v-if="form.errors.mensaje" class="contact-error">{{ form.errors.mensaje }}</p>
                    </div>

                    <p class="text-gray-400 text-xs leading-relaxed">
                        Al enviar este formulario aceptás que tratemos tus datos para responder tu consulta,
                        según nuestra <Link :href="route('legal.privacidad')" class="legal-link">política de privacidad</Link>.
                    </p>

                    <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="form.processing">
                        {{ form.processing ? 'Enviando...' : 'Enviar mensaje' }}
                    </button>
                </form>

                <p class="text-center text-gray-500 text-sm mt-6">
                    También podés escribirnos a
                    <a :href="`mailto:${contactEmail}`" class="legal-link">{{ contactEmail }}</a>
                </p>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PublicBackLink from '@/Components/PublicBackLink.vue';

defineProps<{
    contactEmail: string;
}>();

const page = usePage();
const enviado = ref(false);

const form = useForm({
    nombre: '',
    email: '',
    asunto: '',
    mensaje: '',
});

watch(() => page.props.flash?.success, (mensaje) => {
    if (mensaje) {
        enviado.value = true;
    }
}, { immediate: true });

function enviar() {
    form.post(route('contacto.store'), {
        preserveScroll: true,
        onSuccess: () => {
            enviado.value = true;
        },
    });
}

function nuevoMensaje() {
    router.get(route('contacto.index'));
}
</script>
