import type { UseFormSubmitOptions } from "@inertiajs/core";
import { useForm } from "@inertiajs/vue3";
import type { ProjectID, ProjectMemberID } from "./types";

export function useProjectMemberDelete() {
    const form = useForm();

    const remove = (id: ProjectID, memberId: ProjectMemberID, options?: UseFormSubmitOptions) => {
        form.delete(
            route('projects.memeber.delete', { project: id, member: memberId}),
            options
        );
    }

    return { form, remove }
}