export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface AnalyticsProps {
    google_id: string | null;
    clarity_id: string | null;
    enabled: boolean;
}

export interface FlashProps {
    success: string | null;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    analytics: AnalyticsProps;
    flash: FlashProps;
};
