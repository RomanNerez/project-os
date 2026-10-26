import { useForm } from '@inertiajs/vue3';
import { emptyUserDraft, type UserDraft } from './types';
import { userRoutes } from '../api/userRoutes';

export function useCreateUserForm(onDone: () => void) {
    const form = useForm<UserDraft>(emptyUserDraft());

    function reset(): void {
        form.reset();
        form.clearErrors();
    }

    function submit(): void {
        form.post(userRoutes.store(), {
            onSuccess: onDone,
            preserveScroll: true,
        });
    }

    return { form, submit, reset };
}
