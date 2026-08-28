<script setup lang="ts">
import { toRefs } from 'vue';
import { STATUS_OPTIONS, useTaskForm, type Task, type TaskAssignee, type TaskProject } from '@/entities/task';
import { FormEditor, FormSelect, FormText } from '@/shared/ui';

const props = defineProps<{
    task: Task | null;
    projects: TaskProject[];
    assignees: TaskAssignee[];
}>();

const emit = defineEmits(['onDone', 'onCancel']);

const { task } = toRefs(props);

const visible = defineModel<boolean>('visible', { required: true });

const { form, submit, reset } = useTaskForm(
    task,
    () => {
        emit('onDone');
        reset()
    },
);

function cancel() {
    form.reset();
    emit('onCancel')
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        header="Нова задача"
        :style="{ width: '25rem' }"
    >
        <FormText
            v-model="form.title"
            autofocus
            fluid
            label="Назва"
            placeholder="Назва задачі"
            :message="form.errors.title"
        />
        <template #footer>
            <div class="mt-2 flex justify-end gap-2">
                <Button
                    type="button"
                    label="Скасувати"
                    severity="secondary"
                    text :disabled="form.processing"
                    @click="cancel"
                />
                <Button type="button" label="Створити" :loading="form.processing" @click="submit" />
            </div>
        </template>
    </Dialog>
</template>
