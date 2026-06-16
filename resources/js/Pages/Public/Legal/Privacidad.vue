<template>
    <LegalLayout title="Política de privacidad" updated="10 de junio de 2026">
        <p>
            En {{ siteName }} respetamos tu privacidad y tratamos tus datos personales conforme al
            Reglamento (UE) 2016/679 (RGPD) y la Ley Orgánica 3/2018 de Protección de Datos (LOPDGDD).
        </p>

        <h2>Responsable del tratamiento</h2>
        <ul>
            <li><strong>Responsable:</strong> {{ holder }}</li>
            <li><strong>Correo:</strong> <a :href="`mailto:${contactEmail}`">{{ contactEmail }}</a></li>
            <li><strong>Sitio web:</strong> <a :href="siteUrl">{{ siteUrl }}</a></li>
        </ul>

        <h2>Datos que recogemos</h2>
        <p>Según cómo uses el sitio, podemos tratar:</p>
        <ul>
            <li><strong>Datos de navegación:</strong> páginas visitadas, eventos de uso, scroll y origen del tráfico (solo si aceptás cookies de medición).</li>
            <li><strong>Ubicación aproximada:</strong> coordenadas GPS si activás el botón de ubicación en tu dispositivo. Se guardan en la sesión del navegador y se envían al chat para ordenar resultados por cercanía.</li>
            <li><strong>Mensajes del chat:</strong> el texto que escribís en el asistente, que se procesa para generar una respuesta.</li>
            <li><strong>Datos de registro (CMS):</strong> si tenés cuenta de administración, nombre y correo electrónico.</li>
        </ul>

        <h2>Finalidades y base legal</h2>
        <ul>
            <li><strong>Prestar el servicio</strong> (información, chat, listados): ejecución de medidas precontractuales o interés legítimo.</li>
            <li><strong>Medición y mejora</strong> (Google Analytics, Microsoft Clarity): consentimiento, que podés retirar en cualquier momento.</li>
            <li><strong>Ubicación opcional:</strong> consentimiento al activar la función en tu dispositivo.</li>
            <li><strong>Gestión del CMS:</strong> ejecución del contrato o relación con colaboradores.</li>
        </ul>

        <h2>Destinatarios y encargados</h2>
        <p>Podemos compartir datos con proveedores que nos prestan servicios tecnológicos:</p>
        <ul>
            <li><strong>Google Analytics</strong> (Google LLC) — estadísticas de uso.</li>
            <li><strong>Microsoft Clarity</strong> (Microsoft Corporation) — mapas de calor y grabaciones de sesión anonimizadas.</li>
            <li><strong>Groq</strong> — procesamiento de mensajes del chat para generar respuestas.</li>
            <li><strong>Proveedor de hosting</strong> — alojamiento del sitio y base de datos.</li>
        </ul>
        <p>
            Algunos proveedores pueden estar fuera del Espacio Económico Europeo. En esos casos aplicamos
            garantías adecuadas conforme al RGPD (cláusulas contractuales tipo u otras medidas equivalentes).
        </p>

        <h2>Plazos de conservación</h2>
        <ul>
            <li>Datos de medición: según la política de cada herramienta o hasta que retires el consentimiento.</li>
            <li>Ubicación en sesión: mientras dure la sesión del navegador o hasta que la desactives.</li>
            <li>Mensajes del chat: el tiempo necesario para procesar la conversación; no construimos un historial permanente identificable salvo que lo indiquemos expresamente en el futuro.</li>
            <li>Datos de administración: mientras dure la relación y los plazos legales aplicables.</li>
        </ul>

        <h2>Tus derechos</h2>
        <p>Podés ejercer los derechos de acceso, rectificación, supresión, oposición, limitación y portabilidad escribiendo a
            <a :href="`mailto:${contactEmail}`">{{ contactEmail }}</a>.
            También podés presentar una reclamación ante la Agencia Española de Protección de Datos (www.aepd.es).
        </p>

        <h2>Menores</h2>
        <p>El sitio no está dirigido a menores de 14 años. Si detectamos datos de menores sin consentimiento parental, los eliminaremos.</p>

        <p>
            Más información sobre cookies en nuestra
            <Link :href="route('legal.cookies')" class="legal-link">política de cookies</Link>.
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
