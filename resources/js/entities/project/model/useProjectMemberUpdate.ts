import type { UseFormSubmitOptions } from "@inertiajs/core";
import { useForm } from "@inertiajs/vue3";
import type { MemberRole, ProjectID, ProjectMember, ProjectMemberID } from "./types";

export function useProjectMemberUpdate() {
    const form = useForm({
        role: null as MemberRole | null,
        hourly_rate: null as number | null,
    });

    const fill = (member: ProjectMember) => {
        form.defaults({ role: member.role, hourly_rate: member.hourly_rate ?? null });
        form.reset();
        form.clearErrors();
    }

    const update = (id: ProjectID, memberId: ProjectMemberID, options?: UseFormSubmitOptions) => {
        form.patch(
            route('projects.memeber.update', { project: id, member: memberId }),
            options
        );
    }

    return { form, fill, update }
}
