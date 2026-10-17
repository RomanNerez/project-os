import { useForm } from '@inertiajs/vue3';
import { userRoutes, type UserProfile } from '@/entities/user';

interface ProfileInfoDraft {
    first_name: string;
    last_name: string;
    avatar: File | null;
    remove_avatar: boolean;
}

export function useUpdateProfile(user: UserProfile) {
    const form = useForm<ProfileInfoDraft>({
        first_name: user.first_name ?? '',
        last_name: user.last_name ?? '',
        avatar: null,
        remove_avatar: false,
    });

    function submit(): void {
        form.post(userRoutes.updateProfile(), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                form.defaults({ first_name: form.first_name, last_name: form.last_name, avatar: null, remove_avatar: false });
                form.reset('avatar', 'remove_avatar');
            },
        });
    }

    return { form, submit };
}
