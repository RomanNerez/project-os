import { watch, type Ref } from "vue";
import { emptyTaskDraft, toDraft, type Task, type TaskDraft } from "./types";
import { useForm } from "@inertiajs/vue3";
import { taskRoutes } from "../api/taskRoutes";

export function useTaskForm(task: Ref<Task | null>, onDone: () => void) {
    const form = useForm<TaskDraft>(emptyTaskDraft());
    const options = { onSuccess: onDone, preserveScroll: true };

    function reset(): void {
        form.defaults(task.value ? toDraft(task.value) : emptyTaskDraft());
        form.reset();
        form.clearErrors();
    }

    watch(task, reset, { immediate: true });

    function submit(): void {
        task.value
            ? form.put(taskRoutes.update(task.value.id), options)
            : form.post(taskRoutes.store(), options);
    };

    function remove(): void {
        if (!task.value) return;

        form.delete(taskRoutes.destroy(task.value.id), options);
    }

    return { form, submit, remove, reset };
}
