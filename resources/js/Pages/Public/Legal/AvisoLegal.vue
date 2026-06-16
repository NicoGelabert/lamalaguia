<template>
    <LegalLayout title="Aviso legal" updated="10 de junio de 2026">
        <p>
            En cumplimiento de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información
            y de Comercio Electrónico (LSSI-CE), se informa a los usuarios de los datos identificativos del titular
            de este sitio web.
        </p>

        <h2>Titular del sitio</h2>
        <ul>
            <li><strong>Titular:</strong> {{ holder }}</li>
            <li><strong>Sitio web:</strong> <a :href="siteUrl">{{ siteUrl }}</a></li>
            <li><strong>Correo de contacto:</strong> <a :href="`mailto:${contactEmail}`">{{ contactEmail }}</a></li>
        </ul>

        <h2>Objeto</h2>
        <p>
            {{ siteName }} es una guía digital orientada a la comunidad argentina en Málaga y Andalucía.
            El sitio ofrece información sobre trámites, negocios, eventos y un asistente conversacional con inteligencia artificial
            que utiliza datos publicados en la plataforma.
        </p>

        <h2>Condiciones de uso</h2>
        <p>
            El acceso y uso de este sitio implica la aceptación de las condiciones recogidas en el
            <Link :href="route('legal.terminos')" class="legal-link">aviso de términos y condiciones</Link>,
            la <Link :href="route('legal.privacidad')" class="legal-link">política de privacidad</Link>
            y la <Link :href="route('legal.cookies')" class="legal-link">política de cookies</Link>.
        </p>

        <h2>Propiedad intelectual</h2>
        <p>
            Los contenidos del sitio (textos, diseño, logotipos, código y bases de datos) son propiedad del titular
            o de terceros con licencia, y están protegidos por la normativa de propiedad intelectual e industrial.
            Queda prohibida su reproducción o distribución sin autorización expresa.
        </p>

        <h2>Responsabilidad</h2>
        <p>
            La información publicada tiene carácter orientativo. El titular no garantiza la ausencia de errores
            ni la disponibilidad ininterrumpida del servicio, sin perjuicio de las acciones diligentes para mantenerlo.
        </p>
    </LegalLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import LegalLayout from './LegalLayout.vue';

defineProps<{
    siteName: string;
    siteUrl: string;
    contactEmail: string;
    holder: string;
}>();
</script>
