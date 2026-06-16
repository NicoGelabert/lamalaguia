export interface MessagePart {
    type: 'text' | 'link';
    content: string;
    href?: string;
}

interface RawMatch {
    start: number;
    end: number;
    label: string;
    href: string;
}

const SLUG = '[a-z0-9]+(?:-[a-z0-9]+)*';
const INTERNAL_SECTIONS = 'negocios|tramites|eventos';
const INTERNAL_PATH = new RegExp(`\\/(?:${INTERNAL_SECTIONS})(?:\\/${SLUG})?`, 'gi');

const MARKDOWN_LINK = new RegExp(
    `\\[([^\\]]+)\\]\\(((?:https?:\\/\\/[^)]+)|\\/(?:${INTERNAL_SECTIONS})(?:\\/${SLUG})?)\\)`,
    'gi',
);
const LABELED_GUIA = new RegExp(
    `(?:Guía|guía|Ficha|ficha)\\s*:\\s*(\\/(?:${INTERNAL_SECTIONS})(?:\\/${SLUG})?)`,
    'gi',
);
const LABELED_WHATSAPP = /(?:whatsapp|WhatsApp)\s*(?::|al|a)\s*([+\d][\d\s()-]{7,})/gi;
const LABELED_TEL = /(?:Tel(?:éfono)?|Tlf\.?|tel)\s*:\s*([+\d][\d\s()-]{7,})/gi;
const LABELED_WEB = /(?:Web|web|Sitio)\s*:\s*(https?:\/\/[^\s]+|www\.[^\s]+)/gi;
const URL = /https?:\/\/[^\s<>"']+/gi;
const WWW = /www\.[a-zA-Z0-9][-a-zA-Z0-9.]*\.[a-zA-Z]{2,}(?:\/[^\s<>"']*)?/gi;

function trimTrailingPunctuation(value: string): string {
    return value.replace(/[.,;:!?)]+$/, '');
}

function digitsOnly(value: string): string {
    return value.replace(/\D/g, '');
}

function normalizeTel(value: string): string {
    const cleaned = value.replace(/[^\d+]/g, '');
    return cleaned.startsWith('+') ? cleaned : `+${digitsOnly(cleaned)}`;
}

function collectMatches(text: string, regex: RegExp, build: (match: RegExpExecArray) => RawMatch | null): RawMatch[] {
    const results: RawMatch[] = [];
    const re = new RegExp(regex.source, regex.flags);
    let match: RegExpExecArray | null;

    while ((match = re.exec(text)) !== null) {
        const built = build(match);
        if (built) {
            results.push(built);
        }
    }

    return results;
}

function findMatches(text: string): RawMatch[] {
    const matches: RawMatch[] = [
        ...collectMatches(text, MARKDOWN_LINK, (m) => ({
            start: m.index,
            end: m.index + m[0].length,
            label: m[1],
            href: trimTrailingPunctuation(m[2]),
        })),
        ...collectMatches(text, LABELED_GUIA, (m) => ({
            start: m.index,
            end: m.index + m[0].length,
            label: m[0],
            href: trimTrailingPunctuation(m[1]),
        })),
        ...collectMatches(text, INTERNAL_PATH, (m) => {
            const href = trimTrailingPunctuation(m[0]);

            return {
                start: m.index,
                end: m.index + m[0].length,
                label: href,
                href,
            };
        }),
        ...collectMatches(text, LABELED_WHATSAPP, (m) => {
            const digits = digitsOnly(m[1]);
            if (digits.length < 8) {
                return null;
            }

            return {
                start: m.index,
                end: m.index + m[0].length,
                label: m[0],
                href: `https://wa.me/${digits}`,
            };
        }),
        ...collectMatches(text, LABELED_TEL, (m) => ({
            start: m.index,
            end: m.index + m[0].length,
            label: m[0],
            href: `tel:${normalizeTel(m[1])}`,
        })),
        ...collectMatches(text, LABELED_WEB, (m) => {
            const url = trimTrailingPunctuation(m[1]);
            const href = url.startsWith('http') ? url : `https://${url}`;

            return {
                start: m.index,
                end: m.index + m[0].length,
                label: m[0],
                href,
            };
        }),
        ...collectMatches(text, URL, (m) => {
            const url = trimTrailingPunctuation(m[0]);

            return {
                start: m.index,
                end: m.index + m[0].length,
                label: url,
                href: url,
            };
        }),
        ...collectMatches(text, WWW, (m) => {
            const url = trimTrailingPunctuation(m[0]);

            return {
                start: m.index,
                end: m.index + m[0].length,
                label: url,
                href: `https://${url}`,
            };
        }),
    ];

    matches.sort((a, b) => a.start - b.start || b.end - a.end);

    const accepted: RawMatch[] = [];
    let lastEnd = 0;

    for (const match of matches) {
        if (match.start < lastEnd) {
            continue;
        }

        accepted.push(match);
        lastEnd = match.end;
    }

    return accepted;
}

export function linkifyMessage(text: string): MessagePart[] {
    if (!text) {
        return [{ type: 'text', content: '' }];
    }

    const matches = findMatches(text);

    if (matches.length === 0) {
        return [{ type: 'text', content: text }];
    }

    const parts: MessagePart[] = [];
    let cursor = 0;

    for (const match of matches) {
        if (match.start > cursor) {
            parts.push({ type: 'text', content: text.slice(cursor, match.start) });
        }

        parts.push({
            type: 'link',
            content: match.label,
            href: match.href,
        });

        cursor = match.end;
    }

    if (cursor < text.length) {
        parts.push({ type: 'text', content: text.slice(cursor) });
    }

    return parts;
}

export function isInternalHref(href: string): boolean {
    return /^\/(?:negocios|tramites|eventos)(?:\/[a-z0-9]+(?:-[a-z0-9]+)*)?$/.test(href);
}

export function isExternalHref(href: string): boolean {
    return href.startsWith('http://') || href.startsWith('https://');
}
