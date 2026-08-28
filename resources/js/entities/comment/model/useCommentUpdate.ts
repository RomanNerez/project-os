import { useForm } from "@inertiajs/vue3";
import type { UseFormSubmitOptions } from "@inertiajs/core";
import { commentRoutes } from "../api/commentRoutes";
import type { CommentID } from "./types";

export function useCommentUpdate(body: string = '') {
    const form = useForm({ body });

    const update = (id: CommentID, options?: UseFormSubmitOptions) => {
        form.patch(commentRoutes.update(id), options);
    }

    return { form, update };
}