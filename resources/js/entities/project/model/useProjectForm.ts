import { watch, type Ref } from "vue";
import { emptyProjectDraft, toDraft, type Project, type ProjectDraft } from "./types";
import { useForm } from "@inertiajs/vue3";
import { projectRoutes } from "../api/projectRoutes";

export function useProjectForm(project: Ref<Project | null>, onDone: () => void) {
    const form = useForm<ProjectDraft>(emptyProjectDraft());
    const options = { onSuccess: onDone, preserveScroll: true };

    watch(project, (value) => {
        form.defaults(value ? toDraft(value) : emptyProjectDraft());
        form.reset();
        form.clearErrors();
    }, { immediate: true });

    function submit(): void {
        project.value
            ? form.put(projectRoutes.update(project.value.id), options)
            : form.post(projectRoutes.store(), options);
    };

    function remove(): void {
        if (!project.value) return;

        form.delete(projectRoutes.destroy(project.value.id), options);
    }

    return { form, submit, remove };
}
