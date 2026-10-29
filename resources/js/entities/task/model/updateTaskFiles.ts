import { router} from "@inertiajs/vue3";
import type { VisitOptions } from '@inertiajs/core';
import { taskRoutes } from "../api/taskRoutes";
import type { TaskID } from "./types";

export function updateTaskFiles(projectId: number, id: TaskID, files: File[], options?: Partial<VisitOptions>): void {
    router.post(taskRoutes.uploadFiles(projectId, id), { files }, {
        preserveScroll: true,
        preserveState: true,
        ...options,
    });
}
