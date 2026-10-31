<script setup lang="ts">
import { toRefs } from 'vue';
import { useRoleForm, type WorkspaceRole } from '@/entities/role';
import { FormText, FormTextarea } from '@/shared/ui';

/** Permissions are edited in the roles matrix; the form keeps the role's current ones. */
const props = defineProps<{
    role: WorkspaceRole | null;
}>();

const emit = defineEmits<{
    done: [];
    cancel: [];
}>();

const { role } = toRefs(props);

const visible = defineModel<boolean>('visible', { required: true });

const { form, submit, reset } = useRoleForm(role, () => {
    visible.value = false;
    emit('done');
    reset();
});

function cancel(): void {
    visible.value = false;
    reset();
    emit('cancel');
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :header="role ? 'Редагувати роль' : 'Нова роль'"
        :style="{ width: '32rem' }"
        :breakpoints="{ '640px': '95vw' }"
        @hide="reset"
    >
        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <FormText
                v-model="form.display_name"
                autofocus
                fluid
                name="display_name"
                label="Назва"
                placeholder="Наприклад, Менеджер"
                :message="form.errors.display_name"
            />

            <FormTextarea
                v-model="form.description"
                name="description"
                label="Опис"
                :message="form.errors.description"
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
                    :label="role ? 'Зберегти' : 'Створити'"
                    :loading="form.processing"
                    @click="submit"
                />
            </div>
        </template>
    </Dialog>
</template>
