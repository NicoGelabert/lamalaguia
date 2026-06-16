import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const CONSENT_KEY = 'lamalaguia_cookie_consent';
const LEGACY_CONSENT_KEY = 'lamalaguia_analytics_consent';
const SCROLL_MILESTONES = [25, 50, 75, 90, 100];
const CONSENT_VERSION = 1;

let scrollHandler: (() => void) | null = null;
let trackedDepths = new Set<number>();
let clarityLoaded = false;

export type AnalyticsEventParams = Record<string, string | number | boolean>;

export interface AnalyticsConfig {
    googleId: string | null;
    clarityId: string | null;
}

export interface CookiePreferences {
    version: number;
    choice: 'all' | 'custom' | 'rejected';
    analytics: boolean;
    clarity: boolean;
    updatedAt: string;
}

export const cookieBannerVisible = ref(false);
export const cookieSettingsVisible = ref(false);

export function isAnalyticsConfigured(): boolean {
    return Boolean(window.__ANALYTICS__?.googleId || window.__ANALYTICS__?.clarityId);
}

export function syncAnalyticsConfig(config?: AnalyticsConfig): void {
    if (!config) {
        return;
    }

    window.__ANALYTICS__ = {
        googleId: config.googleId ?? null,
        clarityId: config.clarityId ?? null,
    };
}

function migrateLegacyConsent(): CookiePreferences | null {
    const legacy = localStorage.getItem(LEGACY_CONSENT_KEY);
    if (!legacy) {
        return null;
    }

    localStorage.removeItem(LEGACY_CONSENT_KEY);

    if (legacy === 'granted') {
        return savePreferences({ choice: 'all', analytics: true, clarity: true });
    }

    return savePreferences({ choice: 'rejected', analytics: false, clarity: false });
}

export function getPreferences(): CookiePreferences | null {
    const raw = localStorage.getItem(CONSENT_KEY);
    if (!raw) {
        return migrateLegacyConsent();
    }

    try {
        return JSON.parse(raw) as CookiePreferences;
    } catch {
        localStorage.removeItem(CONSENT_KEY);
        return null;
    }
}

function savePreferences(prefs: Omit<CookiePreferences, 'version' | 'updatedAt'>): CookiePreferences {
    const stored: CookiePreferences = {
        version: CONSENT_VERSION,
        updatedAt: new Date().toISOString(),
        ...prefs,
    };
    localStorage.setItem(CONSENT_KEY, JSON.stringify(stored));
    return stored;
}

export function hasAnsweredConsent(): boolean {
    return getPreferences() !== null;
}

export function needsConsentBanner(): boolean {
    return !hasAnsweredConsent();
}

export function hasAnalyticsConsent(): boolean {
    return getPreferences()?.analytics === true;
}

export function hasClarityConsent(): boolean {
    return getPreferences()?.clarity === true;
}

export function openCookieSettings(): void {
    cookieBannerVisible.value = true;
    cookieSettingsVisible.value = true;
}

export function closeCookieUi(): void {
    cookieBannerVisible.value = false;
    cookieSettingsVisible.value = false;
}

function updateGtagConsent(analytics: boolean): void {
    if (!window.gtag) {
        return;
    }

    window.gtag('consent', 'update', {
        analytics_storage: analytics ? 'granted' : 'denied',
    });
}

export function loadClarity(): void {
    const projectId = window.__ANALYTICS__?.clarityId;
    if (!projectId || clarityLoaded || document.getElementById('clarity-script')) {
        return;
    }

    const w = window as Window & { clarity?: (...args: unknown[]) => void };
    w.clarity = w.clarity || function (...args: unknown[]) {
        (w.clarity as unknown as { q: unknown[] }).q = (w.clarity as unknown as { q: unknown[] }).q || [];
        (w.clarity as unknown as { q: unknown[] }).q.push(args);
    };

    const script = document.createElement('script');
    script.async = true;
    script.src = 'https://www.clarity.ms/tag/' + projectId;
    script.id = 'clarity-script';
    document.head.appendChild(script);

    clarityLoaded = true;
}

function removeClarity(): void {
    document.getElementById('clarity-script')?.remove();
    clarityLoaded = false;
    delete (window as Window & { clarity?: unknown }).clarity;
}

export function applyPreferences(prefs: Omit<CookiePreferences, 'version' | 'updatedAt'>): void {
    const stored = savePreferences(prefs);

    updateGtagConsent(stored.analytics);

    if (stored.clarity && window.__ANALYTICS__?.clarityId) {
        loadClarity();
    } else {
        removeClarity();
    }

    if (stored.analytics || stored.clarity) {
        trackPageView(window.location.pathname);
        resetScrollTracking();
    } else if (scrollHandler) {
        resetScrollTracking();
    }

    closeCookieUi();
}

export function acceptAllCookies(): void {
    applyPreferences({ choice: 'all', analytics: true, clarity: true });
}

export function rejectAllCookies(): void {
    applyPreferences({ choice: 'rejected', analytics: false, clarity: false });
}

export function saveCustomPreferences(analytics: boolean, clarity: boolean): void {
    applyPreferences({
        choice: 'custom',
        analytics,
        clarity,
    });
}

export function trackPageView(path: string, title?: string): void {
    if (!hasAnalyticsConsent() || !window.gtag || !window.__ANALYTICS__?.googleId) {
        return;
    }

    window.gtag('event', 'page_view', {
        page_path: path,
        page_title: title ?? document.title,
    });
}

export function trackEvent(event: string, params: AnalyticsEventParams = {}): void {
    if (hasAnalyticsConsent() && window.gtag) {
        window.gtag('event', event, params);
    }

    if (hasClarityConsent() && window.clarity) {
        window.clarity('event', event);
    }
}

export function resetScrollTracking(): void {
    trackedDepths.clear();

    if (scrollHandler) {
        window.removeEventListener('scroll', scrollHandler, { passive: true } as EventListenerOptions);
        scrollHandler = null;
    }

    if (!hasAnalyticsConsent()) {
        return;
    }

    scrollHandler = () => {
        const height = document.documentElement.scrollHeight - window.innerHeight;
        if (height <= 0) {
            return;
        }

        const percent = Math.round((window.scrollY / height) * 100);

        for (const milestone of SCROLL_MILESTONES) {
            if (percent >= milestone && !trackedDepths.has(milestone)) {
                trackedDepths.add(milestone);
                trackEvent('scroll_depth', {
                    percent: milestone,
                    page_path: window.location.pathname,
                });
            }
        }
    };

    window.addEventListener('scroll', scrollHandler, { passive: true });
}

export function initAnalytics(): void {
    if (!isAnalyticsConfigured()) {
        return;
    }

    router.on('navigate', (event) => {
        const path = new URL(event.detail.page.url, window.location.origin).pathname;
        trackPageView(path);
        resetScrollTracking();
    });

    const prefs = getPreferences();
    if (prefs) {
        updateGtagConsent(prefs.analytics);
        if (prefs.clarity) {
            loadClarity();
        }
        if (prefs.analytics || prefs.clarity) {
            trackPageView(window.location.pathname);
            resetScrollTracking();
        }
    }
}
