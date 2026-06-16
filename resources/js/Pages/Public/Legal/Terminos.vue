<template>
    <LegalLayout title="Términos y condiciones" updated="10 de junio de 2026">
        <p>
            Los presentes términos regulan el acceso y uso de {{ siteName }} ({{ siteUrl }}).
            Al utilizar el sitio, aceptás estas condiciones.
        </p>

        <h2>1. Descripción del servicio</h2>
        <p>
            {{ siteName }} es una guía informativa para argentinos en Málaga y Andalucía. Incluye listados de trámites,
            negocios y eventos, además de un chat con inteligencia artificial que responde en base a la información
            publicada en la plataforma.
        </p>

        <h2>2. Naturaleza orientativa</h2>
        <p>
            La información del sitio y las respuestas del chat tienen carácter <strong>orientativo</strong>.
            No sustituyen asesoramiento legal, migratorio, fiscal, médico ni profesional.
            Antes de tomar decisiones importantes, consultá fuentes oficiales o profesionales cualificados.
        </p>

        <h2>3. Uso permitido</h2>
        <p>Te comprometés a:</p>
        <ul>
            <li>Usar el sitio de forma lícita y respetuosa.</li>
            <li>No intentar acceder sin autorización a áreas restringidas (como el panel de administración).</li>
            <li>No utilizar el chat para enviar contenido ilícito, ofensivo o que vulnere derechos de terceros.</li>
            <li>No realizar scraping masivo ni sobrecargar los sistemas.</li>
        </ul>

        <h2>4. Chat con inteligencia artificial</h2>
        <p>
            El asistente puede cometer errores o responder de forma incompleta. Las respuestas se basan en los datos
            disponibles en el momento de la consulta. No garantizamos exactitud absoluta ni disponibilidad ininterrumpida
            del servicio de IA.
        </p>

        <h2>5. Contenido de terceros</h2>
        <p>
            Los negocios, eventos y enlaces externos publicados pertenecen a terceros. {{ siteName }} no se responsabiliza
            por la calidad, disponibilidad o veracidad de los servicios ofrecidos por esos terceros, aunque procuramos
            mantener información actualizada.
        </p>

        <h2>6. Propiedad intelectual</h2>
        <p>
            Queda prohibida la reproducción, distribución o transformación de los contenidos del sitio sin autorización,
            salvo uso privado o citas breves con mención de la fuente.
        </p>

        <h2>7. Limitación de responsabilidad</h2>
        <p>
            En la medida permitida por la ley, el titular no será responsable de daños derivados del uso del sitio,
            de decisiones tomadas en base a su contenido, de interrupciones del servicio o de virus informáticos
            ajenos a su control diligente.
        </p>

        <h2>8. Modificaciones</h2>
        <p>
            Podemos actualizar estos términos y el contenido del sitio. Los cambios relevantes se publicarán en esta página
            con indicación de la fecha de actualización.
        </p>

        <h2>9. Legislación y jurisdicción</h2>
        <p>
            Estas condiciones se rigen por la legislación española. Para cualquier controversia, las partes se someten
            a los juzgados y tribunales que correspondan según la normativa de consumidores y usuarios, cuando sea aplicable.
        </p>

        <h2>10. Contacto</h2>
        <p>
            Para consultas sobre estos términos: <a :href="`mailto:${contactEmail}`">{{ contactEmail }}</a>.
            Consultá también el <Link :href="route('legal.aviso')" class="legal-link">aviso legal</Link>
            y la <Link :href="route('legal.privacidad')" class="legal-link">política de privacidad</Link>.
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
