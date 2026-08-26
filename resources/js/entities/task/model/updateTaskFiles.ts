import { router} from "@inertiajs/vue3";
import type { VisitOptions } from '@inertiajs/core';
import { taskRoutes } from "../api/taskRoutes";
import type { TaskID } from "./types";

export function updateTaskFiles(id: TaskID, files: File[], options?: Partial<VisitOptions>): void {
    router.post(taskRoutes.uploadFiles(id), { files }, {
        preserveScroll: true,
        preserveState: true,
        ...options,
    });
}
