import type { User } from "./types";

declare module '@inertiajs/core' {
    interface PageProps {
        ai_agent: {
            messages: User | null;
        };
    }
}