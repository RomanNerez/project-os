<script setup lang="ts">
import { computed, watch } from 'vue';
import { roleLabel, type WorkspaceRole } from '@/entities/role';
import { useCreateUserForm } from '@/entities/user';
import { FormPassword, FormSelect, FormText } from '@/shared/ui';

const props = withDefaults(defineProps<{
    /** Workspace roles to pick from; the field is hidden when empty. */
    roles?: WorkspaceRole[];
}>(), {
    roles: () => [],
});

const roleOptions = computed(() => props.roles.map((role) => ({ label: roleLabel(role), value: role.id })));

const emit = defineEmits(['onDone', 'onCancel']);

const visible = defineModel<boolean>('visible', { required: true });

const { form, submit, reset } = useCreateUserForm(() => {
    emit('onDone');
    reset();
});

watch(visible, (isVisible) => {
    if (isVisible) reset();
});

function cancel(): void {
    reset();
    emit('onCancel');
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        header="Новий користувач"
        :style="{ width: '32rem' }"
        :breakpoints="{ '640px': '95vw' }"
    >
        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="grid gap-4 sm:grid-cols-2">
                <FormText
                    v-model="form.first_name"
                    autofocus
                    fluid
                    name="first_name"
                    label="Імʼя"
                    :message="form.errors.first_name"
                />
                <FormText
                    v-model="form.last_name"
                    fluid
                    name="last_name"
                    label="Прізвище"
                    :message="form.errors.last_name"
                />
            </div>

            <FormText
                v-model="form.email"
                fluid
                name="email"
                label="Email"
                placeholder="name@example.com"
                :message="form.errors.email"
            />

            <FormPassword
                v-model="form.password"
                name="password"
                label="Пароль"
                :message="form.errors.password"
            />

            <FormPassword
                v-model="form.password_confirmation"
                name="password_confirmation"
                label="Підтвердження пароля"
                :message="form.errors.password_confirmation"
            />

            <FormSelect
                v-if="roleOptions.length"
                v-model="form.role_id"
                name="role_id"
                label="Роль"
                placeholder="Без ролі"
                :options="roleOptions"
                option-label="label"
                option-value="value"
                show-clear
                :message="form.errors.role_id"
            />

            <button type="submit" class="hidden" />
        </form>

        <template #footer>
            <div class="mt-2 flex justify-end gap-2">
                <Button
                    type="button"
                    label="Скасувати"
                    severity="secondary"
                    text
                    :disabled="form.processing"
                    @click="cancel"
                />
                <Button
                    type="button"
                    label="Створити"
                    :loading="form.processing"
                    @click="submit"
                />
            </div>
        </template>
    </Dialog>
</template>
