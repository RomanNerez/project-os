import { ref } from "vue";
import { router, type VisitOptions } from '@inertiajs/core';
import type { TaskID } from "./types";
import { taskRoutes } from "../api/taskRoutes";

export function useTaskDelete() {
    const isDeleting = ref(false);

    const destroy = (projectId: number, id: TaskID, options?: Partial<VisitOptions>) => {
        router.delete(taskRoutes.destroy(projectId, id), {
            preserveScroll: true,
            reset: ['tasks'],
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
