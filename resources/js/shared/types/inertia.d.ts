export interface AuthUser {
    id: number;
    name: string;
    email: string;
}

declare module '@inertiajs/core' {
    interface PageProps {
        auth: {
            user: AuthUser | null;
        };
    }
}
