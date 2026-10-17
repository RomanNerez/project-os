import { computed, watch, type Ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { taskRoutes } from "../api/taskRoutes";
import type { Task, TaskEditableField } from "./types";

interface SaveOptions {
    onSuccess?: () => void;
}

/**
 * Edits a single task field and saves it on its own PATCH request,
 * so every field has independent processing and error state.
 */
export function useTaskFieldForm<K extends TaskEditableField>(
    task: Ref<Task>,
    field: K,
    { revertOnError = false }: { revertOnError?: boolean } = {},
) {
    const form = useForm<{ value: Task[TaskEditableField] }>({ value: task.value[field] });

    const error = computed(() => (form.errors as Record<string, string | undefined>)[field]);

    // form.isDirty is recalculated by an async watcher, so it is stale right after
    // an assignment (e.g. a select change followed by save()). Compare directly instead.
    const isChanged = (): boolean => form.value !== task.value[field];

    watch(
        () => [task.value.id, task.value[field]] as const,
        ([id], previous) => {
            const isOtherTask = previous?.[0] !== id;
            const isEditing = !isOtherTask && form.value !== previous?.[1];

            form.defaults({ value: task.value[field] });

            if (!isEditing) form.reset();
            if (isOtherTask) form.clearErrors();
        },
    );

    function save({ onSuccess }: SaveOptions = {}): void {
        if (form.processing) return;

        if (!isChanged()) {
            onSuccess?.();
            return;
        }

        form
            .transform(({ value }) => ({ [field]: value }))
            .patch(taskRoutes.update(task.value.project_id, task.value.id), {
                preserveScroll: true,
                preserveState: true,
                reset: ['tasks'],
                onSuccess: () => {
                    form.defaults();
                    form.clearErrors();
                    onSuccess?.();
                },
                onError: () => {
                    if (revertOnError) form.reset();
                },
            });
    }

    function cancel(): void {
        form.reset();
        form.clearErrors();
    }

    return { form, error, save, cancel };
}
