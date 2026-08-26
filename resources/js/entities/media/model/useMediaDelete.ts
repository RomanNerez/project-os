import { router} from "@inertiajs/vue3";
import type { VisitOptions } from '@inertiajs/core';
import type { MediaID } from "./types";
import { mediaRoutes } from "../api/mediaRoutes";
import { ref } from "vue";

export function useMediaDelete() {
    const isDeleting = ref(false);

    const destroy = (id: MediaID, options?: Partial<VisitOptions>) => {
        router.delete(mediaRoutes.destroy(id), {
            preserveScroll: true,
            preserveState: true,
            ...options,
            onStart: (visit) => {
                isDeleting.value = true;
                options?.onStart?.(visit);
            },
            onFinish: (visit) => {
                isDeleting.value = false;
                options?.onFinish?.(visit);
            },
        });
    };

    return {
        destroy,
        isDeleting
    };
}