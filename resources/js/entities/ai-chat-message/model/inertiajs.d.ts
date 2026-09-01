import type { PaginatedServerData } from "@/shared/types";
import type { AiChatMessage } from "./types";

declare module '@inertiajs/core' {
    interface PageProps {
        ai_agent: {
            messages: PaginatedServerData<AiChatMessage[]>;
        };
    }
}