export type RedSocialTipo =
    | 'instagram'
    | 'tiktok'
    | 'facebook'
    | 'youtube'
    | 'x'
    | 'the_fork'
    | 'google_business';

export interface RedSocialLink {
    tipo: RedSocialTipo;
    url: string;
}

export const REDES_SOCIALES_OPCIONES: { value: RedSocialTipo; label: string }[] = [
    { value: 'instagram', label: 'Instagram' },
    { value: 'tiktok', label: 'TikTok' },
    { value: 'facebook', label: 'Facebook' },
    { value: 'youtube', label: 'Youtube' },
    { value: 'x', label: 'X' },
    { value: 'the_fork', label: 'The Fork' },
    { value: 'google_business', label: 'Google Business' },
];

export function etiquetaRedSocial(tipo: RedSocialTipo): string {
    return REDES_SOCIALES_OPCIONES.find((opcion) => opcion.value === tipo)?.label ?? tipo;
}

export function primerTipoDisponible(usados: RedSocialTipo[]): RedSocialTipo | null {
    const disponible = REDES_SOCIALES_OPCIONES.find((opcion) => !usados.includes(opcion.value));
    return disponible?.value ?? null;
}
