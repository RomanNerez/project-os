import { router } from "@inertiajs/vue3";
import { taskRoutes } from "../api/taskRoutes";
import type { TaskID, TaskStatus } from "./types";

export function updateTaskStatus(projectId: number, id: TaskID, status: TaskStatus): void {
    router.patch(taskRoutes.update(projectId, id), { status }, {
        preserveScroll: true,
        preserveState: true,
    });
}
