import { useForm } from '@inertiajs/vue3';
import { userRoutes } from '../api/userRoutes';

export function useInviteUserForm(onDone: (email: string) => void) {
    const form = useForm({ email: '' });

    function reset(): void {
        form.reset();
        form.clearErrors();
    }

    function submit(): void {
        form.post(userRoutes.invite(), {
            onSuccess: () => onDone(form.email),
            preserveScroll: true,
        });
    }

    return { form, submit, reset };
}
