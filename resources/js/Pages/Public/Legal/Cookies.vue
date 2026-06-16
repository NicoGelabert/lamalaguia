<template>
    <LegalLayout title="Política de cookies" updated="10 de junio de 2026">
        <p>
            Esta política explica qué cookies y tecnologías similares usa {{ siteName }}, para qué finalidad
            y cómo podés gestionarlas.
        </p>

        <h2>¿Qué son las cookies?</h2>
        <p>
            Son pequeños archivos que se almacenan en tu dispositivo al visitar un sitio web.
            Permiten recordar preferencias, medir el uso o mantener sesiones técnicas.
        </p>

        <h2>¿Qué usamos en este sitio?</h2>

        <h3>1. Cookies técnicas (necesarias)</h3>
        <p>Son imprescindibles para el funcionamiento básico. No requieren consentimiento.</p>
        <div class="legal-table-wrap">
            <table class="legal-table">
                <thead>
                    <tr>
                        <th>Nombre / origen</th>
                        <th>Finalidad</th>
                        <th>Duración</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sesión Laravel / CSRF</td>
                        <td>Seguridad y gestión de sesión</td>
                        <td>Sesión</td>
                    </tr>
                    <tr>
                        <td>lamalaguia_cookie_consent</td>
                        <td>Guardar tu elección sobre cookies</td>
                        <td>1 año</td>
                    </tr>
                    <tr>
                        <td>lamalaguia_ubicacion (sessionStorage)</td>
                        <td>Recordar ubicación si la activás</td>
                        <td>Hasta cerrar pestaña</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>2. Cookies de medición — Google Analytics</h3>
        <p>Requieren tu consentimiento. Nos ayudan a entender el tráfico y el uso del sitio.</p>
        <div class="legal-table-wrap">
            <table class="legal-table">
                <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th>Finalidad</th>
                        <th>Más info</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Google Analytics 4</td>
                        <td>Estadísticas de visitas, páginas vistas y eventos de uso</td>
                        <td><a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Política de Google</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>3. Cookies de medición — Microsoft Clarity</h3>
        <p>Requieren tu consentimiento. Registran interacciones para mapas de calor y mejora de usabilidad.</p>
        <div class="legal-table-wrap">
            <table class="legal-table">
                <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th>Finalidad</th>
                        <th>Más info</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Microsoft Clarity</td>
                        <td>Mapas de calor, grabaciones de sesión y análisis de comportamiento</td>
                        <td><a href="https://privacy.microsoft.com" target="_blank" rel="noopener">Política de Microsoft</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2>Cómo gestionar las cookies</h2>
        <p>Podés en cualquier momento:</p>
        <ul>
            <li>Usar el botón <button type="button" class="legal-link-btn" @click="abrirPreferencias">Gestionar cookies</button> en el pie del sitio.</li>
            <li>Configurar tu navegador para bloquear o eliminar cookies.</li>
            <li>Retirar el consentimiento sin que afecte a la licitud del tratamiento previo.</li>
        </ul>

        <h2>Más información</h2>
        <p>
            Consultá también nuestra
            <Link :href="route('legal.privacidad')" class="legal-link">política de privacidad</Link>.
            Para dudas: <a :href="`mailto:${contactEmail}`">{{ contactEmail }}</a>.
        </p>
    </LegalLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import LegalLayout from './LegalLayout.vue';
import { openCookieSettings } from '@/lib/analytics';

defineProps<{
    siteName: string;
    siteUrl: string;
    contactEmail: string;
    holder: string;
}>();

function abrirPreferencias() {
    openCookieSettings();
}
</script>
