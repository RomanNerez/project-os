<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import { InfiniteScroll } from "@inertiajs/vue3";
import { ProjectLayout, type BreadcrumbItem } from '@/widgets/project-layout';
import { projectRoutes, type Project } from '@/entities/project';
import { STATUS_META, TaskCard, type TaskAssignee } from '@/entities/task';
import { TaskCreateFormModal } from '@/features/task-create-form';
import { TaskDeleteModal } from '@/features/task-delete';
import type { TaskProp } from '../model/types';
import type { PaginatedServerData } from '@/shared/types';
import { EmptyList } from '@/shared/ui';
import { TaskEditFormModal } from '@/widgets/task-edit-form-modal';
import { TaskFiltersPanel, useActiveTaskFilters } from '@/features/task-filters';

interface Props {
    project: {
        data: Project;
    };
    tasks: PaginatedServerData<TaskProp[]>;
}

const props = defineProps<Props>();

const project = computed(() => props.project.data);

const canCreateTasks = computed(() => project.value.permissions.create_task);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { label: 'Проєкти', href: projectRoutes.index() },
    { label: project.value.title, href: projectRoutes.show(project.value.id) },
    { label: 'Задачі' },
]);

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedTaskId = ref<number | null>(null);
const selectedTask = computed<TaskProp | null>(() => props.tasks.data.find(t => t.id === selectedTaskId.value) || null);

// Only the owner and members of the task's project can be assigned.
const selectedTaskAssignees = computed<TaskAssignee[]>(() => {
    const project = selectedTask.value?.project?.data;

    if (!project) return [];

    return [project.user.data, ...(project.members?.data ?? [])]
        .filter((user, index, users) => users.findIndex((u) => u.id === user.id) === index)
        .map(({ id, name }) => ({ id, name }));
});

// A saved field can move the task out of the filtered list.
watch(selectedTask, (task) => {
    if (!task && isEditModalOpen.value) closeEdit();
});

const isActiveFilters = useActiveTaskFilters();

const transformTasks = computed(() => {
    return props.tasks.data.map((t) => {
        let status = null;

        if (t.status) {
            status = STATUS_META[t.status]
        }

        return {
            ...t,
            statusLabel: status?.label ?? '',
            statusColor: status?.severity ?? '',
        }
    })
})

function openCreate(): void {
    selectedTaskId.value = null;
    isCreateModalOpen.value = true;
}

function openEdit(task: TaskProp): void {
    selectedTaskId.value = task.id;
    isEditModalOpen.value = true;
}

function closeEdit(): void {
    selectedTaskId.value = null;
    isEditModalOpen.value = false;
}

function openDelete(task: TaskProp): void {
    selectedTaskId.value = task.id;
    isDeleteModalOpen.value = true;
}
</script>

<template>
    <Head title="Задачі" />

    <ProjectLayout
        title="Задачі"
        :description="`Задачі проєкту «${project.title}»`"
        :breadcrumbs="breadcrumbs"
    >
        <template #actions>
            <Button
                v-if="canCreateTasks"
                label="Нова задача"
                icon="pi pi-plus"
                @click="openCreate"
            />
        </template>

        <div class="flex h-full flex-col gap-4">
            <TaskFiltersPanel />

            <div v-if="transformTasks.length" class="min-h-0 flex-1 overflow-y-auto">
                <InfiniteScroll
                    class="flex flex-col gap-2"
                    data="tasks"
                >
                    <TaskCard
                        v-for="task in transformTasks"
                        :key="task.id"
                        :status-label="task.statusLabel"
                        :status-color="task.statusColor"
                        :title="task.title"
                        :comments-count="task.comments.data.length"
                        :files-count="task.media.data.length"
                        :assignee-name="task.assignee?.data.name ?? ''"
                        :assignee-avatar="task.assignee?.data.avatar_url"
                        :description="task.description"
                        :priority="task.priority"
                        @edit="openEdit(task)"
                        @delete="openDelete(task)"
                    />
                </InfiniteScroll>
            </div>

            <EmptyList
                v-else-if="isActiveFilters"
                icon-class="pi-search"
                decription="Задач за такими фільтрами не знайдено"
                :show-action="false"
            />

            <EmptyList
                v-else
                icon-class="pi-check-square"
                decription="Задач поки немає"
                button-label="Створити першу задачу"
                :show-action="canCreateTasks"
                @on-handler="openCreate"
            />

            <TaskCreateFormModal
                v-model:visible="isCreateModalOpen"
                :project-id="project.id"
                @on-done="() => {
                    selectedTaskId = null;
                    isCreateModalOpen = false;
                }"
                @on-cancel="() => {
                    selectedTaskId = null;
                    isCreateModalOpen = false;
                }"
            />

            <TaskEditFormModal
                v-model:visible="isEditModalOpen"
                :task="selectedTask"
                :media="selectedTask?.media.data ?? []"
                :comments="selectedTask?.comments.data ?? []"
                :time-entries="selectedTask?.time_entries.data ?? []"
                :activities="selectedTask?.activities.data ?? []"
                :assignees="selectedTaskAssignees"
                :can-edit="selectedTask?.project?.data.permissions.update_task ?? false"
                @on-close="closeEdit"
            />

            <TaskDeleteModal
                v-if="selectedTask"
                v-model:visible="isDeleteModalOpen"
                :project-id="selectedTask.project_id"
                :task-id="selectedTask.id"
                :title="selectedTask.title"
                @on-done="() => {
                    selectedTaskId = null;
                    isDeleteModalOpen = false;
                }"
                @on-cancel="() => {
                    selectedTaskId = null;
                    isDeleteModalOpen = false;
                }"
            />
        </div>
    </ProjectLayout>
</template>
