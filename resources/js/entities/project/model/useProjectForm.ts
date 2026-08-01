import { computed, watch, type Ref } from "vue";
import { emptyProjectDraft, toDraft, type Project, type ProjectDraft } from "./types";
import { useForm } from "@inertiajs/vue3";
import { projectRoutes } from "../api/projectRoutes";

export function useProjectForm(project: Ref<Project | null>, onDone: () => void) {
    const form = useForm<ProjectDraft>(emptyProjectDraft());

    watch(project, (value) => {
        form.defaults(value ? toDraft(value) : emptyProjectDraft());
        form.reset();
        form.clearErrors();
    }, { immediate: true });

    const isValid = computed(() => form.title.trim().length > 0);

    function submit(): void {
        if (!isValid.value) return;

        const options = { onSuccess: onDone, preserveScroll: true };

        project.value
            ? form.put(projectRoutes.update(project.value.id), options)
            : form.post(projectRoutes.store(), options);
    }

    return { form, isValid, submit };
}