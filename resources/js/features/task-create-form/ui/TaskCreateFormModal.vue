<script setup lang="ts">
import { computed, toRefs, watch } from 'vue';
import {
    STATUS_META,
    useTaskForm,
    type Task,
    type TaskAssignee,
    type TaskProject,
    type TaskStatus,
} from '@/entities/task';
import { FormText } from '@/shared/ui';

const props = defineProps<{
    task: Task | null;
    projects?: TaskProject[];
    assignees?: TaskAssignee[];
    projectId?: number | null;
    status?: TaskStatus | null;
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
    () => ({
        ...(props.projectId ? { project_id: props.projectId } : {}),
        ...(props.status ? { status: props.status } : {}),
    }),
);

watch(() => [props.projectId, props.status], reset);

const header = computed(() => props.status
    ? `Нова задача · ${STATUS_META[props.status].label}`
    : 'Нова задача');

function cancel() {
    form.reset();
    emit('onCancel')
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :header="header"
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
