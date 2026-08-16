<script setup lang="ts">
import { toRefs } from 'vue';
import { ConfirmModal } from '@/shared/ui';
import { useProjectForm, type Project } from '@/entities/project';

const props = defineProps<{
    project: Project | null;
}>();

const { project } = toRefs(props);

const visible = defineModel<boolean>('visible', { required: true });

const { form, remove } = useProjectForm(project, () => { visible.value = false; });
</script>

<template>
    <ConfirmModal
        v-model:visible="visible"
        header="Видалити проєкт"
        icon="pi pi-exclamation-triangle"
        severity="danger"
        confirm-label="Видалити"
        :processing="form.processing"
        @confirm="remove"
    >
        Ви впевнені, що хочете видалити проєкт
        <span class="font-semibold">«{{ project?.title }}»</span>?
        Цю дію неможливо скасувати.
    </ConfirmModal>
</template>
