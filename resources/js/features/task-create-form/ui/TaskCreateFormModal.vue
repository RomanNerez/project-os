<script setup lang="ts">
import { computed, watch } from 'vue';
import {
    STATUS_META,
    useCreateTask,
    type TaskAssignee,
    type TaskStatus,
} from '@/entities/task';
import { MEMEBER_ROLE, ProjectSelect } from '@/entities/project';
import { FormText } from '@/shared/ui';

const props = defineProps<{
    assignees?: TaskAssignee[];
    projectId?: number | null;
    status?: TaskStatus | null;
}>();

const emit = defineEmits(['onDone', 'onCancel']);

const visible = defineModel<boolean>('visible', { required: true });

const { form, submit, reset } = useCreateTask(
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
        <ProjectSelect
            v-if="!props.projectId"
            v-model="form.project_id"
            class="mt-3"
            label="Проєкт"
            :exclude-roles="[MEMEBER_ROLE.VIEWER]"
            :message="form.errors.project_id"
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
