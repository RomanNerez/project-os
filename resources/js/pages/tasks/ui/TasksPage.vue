<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout } from '@/widgets/admin-layout';
import { STATUS_META, TaskCard, type TaskAssignee, type TaskProject } from '@/entities/task';
import { TaskCreateFormModal } from '@/features/task-create-form';
import { TaskDeleteModal } from '@/features/task-delete';
import type { TaskProp } from '../model/types';
import type { PaginatedServerData } from '@/shared/types';
import { EmptyList } from '@/shared/ui';
import TaskEditFormModal from '@/widgets/task-edit-form-modal/ui/TaskEditFormModal.vue';

interface Props {
    tasks: PaginatedServerData<TaskProp[]>;
    projects: TaskProject[];
    assignees: TaskAssignee[];
}

const props = defineProps<Props>();

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedTaskId = ref<number | null>(null);
const selectedTask = computed<TaskProp | null>(() => props.tasks.data.find(t => t.id === selectedTaskId.value) || null);

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

function openDelete(task: TaskProp): void {
    selectedTaskId.value = task.id;
    isDeleteModalOpen.value = true;
}
</script>

<template>
    <Head title="Задачі" />

    <AdminLayout
        title="Задачі"
        description="Керуйте задачами робочого простору"
    >
        <template #actions>
            <Button label="Нова задача" icon="pi pi-plus" @click="openCreate" />
        </template>

        <div class="flex h-full flex-col gap-4">
            <div v-if="props.tasks.data.length" class="flex flex-col gap-2">
                <TaskCard
                    v-for="task in transformTasks"
                    :key="task.id"
                    :task="task"
                    :status-label="task.statusLabel"
                    :status-color="task.statusColor"
                    :title="task.title"
                    :comments-count="task.comments.data.length"
                    :files-count="task.media.data.length"
                    :assignee-name="task.assignee?.data.name ?? ''"
                    :project-title="task.project?.data.title ?? ''"
                    @edit="openEdit(task)"
                    @delete="openDelete(task)"
                />
            </div>

            <EmptyList
                v-else
                icon-class="pi-check-square"
                decription="Задач поки немає"
                button-label="Створити першу задачу"
                :show-action="!!props.projects.length"
                @on-handler="openCreate"
            />

            <TaskCreateFormModal
                v-model:visible="isCreateModalOpen"
                :task="selectedTask"
                :projects="props.projects"
                :assignees="props.assignees"
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
                :projects="props.projects"
                :assignees="props.assignees"
                @on-done="() => {
                    selectedTaskId = null;
                    isEditModalOpen = false;
                }"
                @on-cancel="() => {
                    selectedTaskId = null;
                    isEditModalOpen = false;
                }"
            />

            <TaskDeleteModal
                v-model:visible="isDeleteModalOpen"
                :task="selectedTask"
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
    </AdminLayout>
</template>
