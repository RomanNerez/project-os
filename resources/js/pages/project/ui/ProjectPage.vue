<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { ProjectLayout, type BreadcrumbItem } from '@/widgets/project-layout';
import { STATUS_META, projectRoutes, type Project } from '@/entities/project';
import { type TaskAssignee, type TaskWithAssignee } from '@/entities/task';
import { ProjectKanban } from '@/widgets/project-kanban';
import { TaskEditFormModal } from '@/widgets/task-edit-form-modal';
import type { ProjectTaskProp } from '../model/types';

interface Props {
    project: {
        data: Project;
    };
    tasks: {
        data: ProjectTaskProp[];
    };
    assignees: TaskAssignee[];
}

const props = defineProps<Props>();

const project = computed(() => props.project.data);

const tasks = computed(() => props.tasks.data);

const status = computed(() => STATUS_META[project.value.status]);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { label: 'Проєкти', href: projectRoutes.index() },
    { label: project.value.title },
]);

const isEditModalOpen = ref(false);
const selectedTaskId = ref<number | null>(null);
const selectedTask = computed<ProjectTaskProp | null>(
    () => tasks.value.find((t) => t.id === selectedTaskId.value) ?? null,
);

function openEdit(task: TaskWithAssignee): void {
    selectedTaskId.value = task.id;
    isEditModalOpen.value = true;
}
</script>

<template>
    <Head :title="project.title" />

    <ProjectLayout
        :title="project.title"
        :description="project.description"
        :breadcrumbs="breadcrumbs"
    >
        <template #actions>
            <Tag :value="status.label" :severity="status.severity" />
        </template>

        <div class="flex h-full flex-col gap-4">
            <ProjectKanban
                :project-id="project.id"
                :tasks="tasks"
                :can-create="project.permissions.create_task"
                :can-edit="project.permissions.update_task"
                @edit="openEdit"
            />
        </div>

        <TaskEditFormModal
            v-model:visible="isEditModalOpen"
            :task="selectedTask"
            :media="selectedTask?.media.data ?? []"
            :comments="selectedTask?.comments.data ?? []"
            :time-entries="selectedTask?.time_entries.data ?? []"
            :activities="selectedTask?.activities.data ?? []"
            :assignees="props.assignees"
            :can-edit="project.permissions.update_task"
            @on-close="isEditModalOpen = false"
        />
    </ProjectLayout>
</template>
