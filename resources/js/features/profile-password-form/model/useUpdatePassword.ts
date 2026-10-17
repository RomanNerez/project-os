import { useForm } from '@inertiajs/vue3';
import { userRoutes } from '@/entities/user';

export function useUpdatePassword() {
    const form = useForm({
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
    });

    function submit(): void {
        form.put(userRoutes.updatePassword(), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
            onError: () => form.reset('current_password', 'new_password', 'new_password_confirmation'),
        });
    }

    return { form, submit };
}
