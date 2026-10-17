import { type Ref } from "vue";
import { type Task } from "./types";
import { useForm } from "@inertiajs/vue3";
import { taskRoutes } from "../api/taskRoutes";

export function useTaskForm(task: Ref<Task | null>, onDone: () => void) {
    const form = useForm({});

    function remove(): void {
        if (!task.value) return;

        form.delete(taskRoutes.destroy(task.value.id), {
            onSuccess: onDone,
            preserveScroll: true,
            reset: ['tasks'],
        });
    }

    return { form, remove };
}
