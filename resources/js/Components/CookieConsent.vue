<template>
    <Teleport to="body">
        <Transition name="slide-up">
            <div v-if="cookieBannerVisible" class="cookie-consent">
                <!-- Panel de gestión -->
                <div v-if="cookieSettingsVisible" class="cookie-settings">
                    <h3 class="cookie-settings-title">Gestionar cookies</h3>
                    <p class="cookie-consent-text">
                        Elegí qué cookies opcionales querés permitir. Las cookies técnicas son necesarias para el funcionamiento del sitio.
                    </p>

                    <div class="cookie-toggle-list">
                        <div class="cookie-toggle-item cookie-toggle-item-disabled">
                            <div>
                                <p class="cookie-toggle-label">Cookies técnicas</p>
                                <p class="cookie-toggle-desc">Necesarias para la seguridad, sesión y recordar tu elección.</p>
                            </div>
                            <span class="cookie-toggle-badge">Siempre activas</span>
                        </div>

                        <label class="cookie-toggle-item">
                            <div>
                                <p class="cookie-toggle-label">Google Analytics</p>
                                <p class="cookie-toggle-desc">Estadísticas de visitas y uso del sitio.</p>
                            </div>
                            <input v-model="prefsAnalytics" type="checkbox" class="cookie-toggle-input" :disabled="!tieneGoogle" />
                        </label>

                        <label class="cookie-toggle-item">
                            <div>
                                <p class="cookie-toggle-label">Microsoft Clarity</p>
                                <p class="cookie-toggle-desc">Mapas de calor y grabaciones de sesión para mejorar la experiencia.</p>
                            </div>
                            <input v-model="prefsClarity" type="checkbox" class="cookie-toggle-input" :disabled="!tieneClarity" />
                        </label>
                    </div>

                    <div class="cookie-consent-links">
                        <Link :href="route('legal.cookies')" class="legal-link" @click="cerrarAlNavegar">Política de cookies</Link>
                        <span class="text-gray-300">·</span>
                        <Link :href="route('legal.privacidad')" class="legal-link" @click="cerrarAlNavegar">Política de privacidad</Link>
                    </div>

                    <div class="cookie-consent-actions">
                        <button type="button" class="btn-outline text-sm" @click="volverAlBanner">
                            Volver
                        </button>
                        <button type="button" class="btn-primary text-sm" @click="guardarPreferencias">
                            Guardar preferencias
                        </button>
                    </div>
                </div>

                <!-- Banner principal -->
                <div v-else>
                    <p class="cookie-consent-text">
                        Usamos cookies propias y de terceros para medir el uso del sitio y mejorar tu experiencia.
                        Podés aceptar todas, rechazarlas o elegir cuáles permitir.
                    </p>

                    <div class="cookie-consent-links">
                        <Link :href="route('legal.cookies')" class="legal-link" @click="cerrarAlNavegar">Política de cookies</Link>
                        <span class="text-gray-300">·</span>
                        <Link :href="route('legal.privacidad')" class="legal-link" @click="cerrarAlNavegar">Política de privacidad</Link>
                    </div>

                    <div class="cookie-consent-actions cookie-consent-actions-main">
                        <button type="button" class="btn-outline text-sm" @click="rechazar">
                            Rechazar todas
                        </button>
                        <button type="button" class="btn-outline text-sm" @click="gestionar">
                            Gestionar cookies
                        </button>
                        <button type="button" class="btn-primary text-sm" @click="aceptar">
                            Aceptar todas
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    acceptAllCookies,
    cookieBannerVisible,
    cookieSettingsVisible,
    getPreferences,
    needsConsentBanner,
    openCookieSettings,
    rejectAllCookies,
    saveCustomPreferences,
    syncAnalyticsConfig,
} from '@/lib/analytics';

const page = usePage();
const prefsAnalytics = ref(true);
const prefsClarity = ref(true);
const tieneGoogle = ref(false);
const tieneClarity = ref(false);

onMounted(() => {
    const { google_id, clarity_id } = page.props.analytics;

    syncAnalyticsConfig({
        googleId: google_id,
        clarityId: clarity_id,
    });

    tieneGoogle.value = Boolean(google_id);
    tieneClarity.value = Boolean(clarity_id);

    const prefs = getPreferences();
    if (prefs) {
        prefsAnalytics.value = prefs.analytics;
        prefsClarity.value = prefs.clarity;
    }

    cookieBannerVisible.value = needsConsentBanner();
});

watch(cookieSettingsVisible, (abierto) => {
    if (!abierto) {
        return;
    }

    const prefs = getPreferences();
    prefsAnalytics.value = prefs?.analytics ?? true;
    prefsClarity.value = prefs?.clarity ?? true;
});

function aceptar() {
    acceptAllCookies();
}

function rechazar() {
    rejectAllCookies();
}

function gestionar() {
    openCookieSettings();
}

function guardarPreferencias() {
    saveCustomPreferences(
        tieneGoogle.value && prefsAnalytics.value,
        tieneClarity.value && prefsClarity.value,
    );
}

function volverAlBanner() {
    cookieSettingsVisible.value = false;
}

function cerrarAlNavegar() {
    cookieBannerVisible.value = false;
    cookieSettingsVisible.value = false;
}
</script>
