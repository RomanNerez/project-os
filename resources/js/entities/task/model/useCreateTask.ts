import { useForm } from "@inertiajs/vue3";
import { emptyTaskCreateDraft, type TaskCreateDraft } from "./types";
import { taskRoutes } from "../api/taskRoutes";

export function useCreateTask(
    onDone: () => void,
    defaults: () => Partial<TaskCreateDraft> = () => ({}),
) {
    const form = useForm<TaskCreateDraft>(emptyTaskCreateDraft());

    function reset(): void {
        form.defaults({ ...emptyTaskCreateDraft(), ...defaults() });
        form.reset();
        form.clearErrors();
    }

    reset();

    function submit(): void {
        if (!form.project_id) {
            form.setError('project_id', 'Оберіть проєкт');
            return;
        }

        form
            .transform(({ title, status }) => ({ title, status }))
            .post(taskRoutes.store(form.project_id), {
                onSuccess: onDone,
                preserveScroll: true,
                reset: ['tasks'],
            });
    }

    return { form, submit, reset };
}
