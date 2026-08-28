import { ref } from "vue";
import { router, type VisitOptions } from '@inertiajs/core';
import type { CommentID } from "./types";
import { commentRoutes } from "../api/commentRoutes";

export function useCommentDelete() {
    const isDeleting = ref(false);

    const destroy = (id: CommentID, options?: Partial<VisitOptions>) => {
        router.delete(commentRoutes.destroy(id), {
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