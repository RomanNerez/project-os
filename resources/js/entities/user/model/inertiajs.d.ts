import type { User } from "./types";

declare module '@inertiajs/core' {
    interface PageProps {
        auth: {
            user: User | null;
        };
    }
}