import { useForm } from "@inertiajs/vue3";
import type { VisitOptions } from '@inertiajs/core';
import type { TaskID } from "./types";
import { taskRoutes } from "../api/taskRoutes";

export function useTaskCommentForm() {
    const form = useForm({ body: '' });

    const submit = (id: TaskID, options?: Partial<VisitOptions>) => {
        form.post(taskRoutes.createTaskComment(id), {
            preserveScroll: true,
            preserveState: true,
            ...options,
        })
    }

    return { form, submit }
}