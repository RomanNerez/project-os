<script setup lang="ts">
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import type { UserProfile } from '@/entities/user';
import { FormText, UserAvatar } from '@/shared/ui';
import { useUpdateProfile } from '../model/useUpdateProfile';

const ACCEPT = 'image/jpeg,image/png,image/webp';

const props = defineProps<{
    user: UserProfile;
}>();

const { form, submit } = useUpdateProfile(props.user);

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

const avatarImage = computed(() => {
    if (previewUrl.value) return previewUrl.value;
    if (form.remove_avatar) return null;

    return props.user.avatar_url;
});

const hasAvatar = computed(() => avatarImage.value !== null);

watch(() => form.avatar, (file) => {
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = file ? URL.createObjectURL(file) : null;
});

onBeforeUnmount(() => {
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
});

function pickFile(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (file) {
        form.avatar = file;
        form.remove_avatar = false;
    }
    input.value = '';
}

function removeAvatar(): void {
    form.avatar = null;
    form.remove_avatar = true;
}
</script>

<template>
    <form class="flex max-w-xl flex-col gap-6" @submit.prevent="submit">
        <div class="flex items-center gap-4">
            <UserAvatar
                :name="`${form.first_name} ${form.last_name}`"
                :image="avatarImage"
                class="!size-20 !text-2xl"
            />
            <div class="flex flex-col gap-2">
                <div class="flex flex-wrap gap-2">
                    <Button
                        type="button"
                        label="Завантажити"
                        icon="pi pi-upload"
                        severity="secondary"
                        size="small"
                        @click="fileInput?.click()"
                    />
                    <Button
                        v-if="hasAvatar"
                        type="button"
                        label="Видалити"
                        icon="pi pi-trash"
                        severity="danger"
                        variant="text"
                        size="small"
                        @click="removeAvatar"
                    />
                </div>
                <small class="text-muted-color">JPG, PNG або WEBP, до 5 МБ</small>
                <Message v-if="form.errors.avatar" severity="error" size="small" variant="simple">
                    {{ form.errors.avatar }}
                </Message>
            </div>
            <input ref="fileInput" type="file" class="hidden" :accept="ACCEPT" @change="pickFile" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <FormText
                v-model="form.first_name"
                name="first_name"
                label="Імʼя"
                placeholder="Тарас"
                :message="form.errors.first_name"
            />
            <FormText
                v-model="form.last_name"
                name="last_name"
                label="Прізвище"
                placeholder="Шевченко"
                :message="form.errors.last_name"
            />
        </div>

        <div class="flex items-center gap-3">
            <Button type="submit" label="Зберегти" icon="pi pi-check" :loading="form.processing" />
            <Message v-if="form.recentlySuccessful" severity="success" size="small" variant="simple">
                Збережено
            </Message>
        </div>
    </form>
</template>
