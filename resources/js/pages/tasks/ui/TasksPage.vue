<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout } from '@/widgets/admin-layout';
import { TaskCard, type Task, type TaskAssignee, type TaskProject } from '@/entities/task';
import { TaskFormModal } from '@/features/task-form';
import { TaskDeleteModal } from '@/features/task-delete';

interface Props {
    tasks: {
        data: Task[];
    };
    projects: TaskProject[];
    assignees: TaskAssignee[];
}

const props = defineProps<Props>();

const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedTask = ref<Task | null>(null);

function openCreate(): void {
    selectedTask.value = null;
    isEditModalOpen.value = true;
}

function openEdit(task: Task): void {
    selectedTask.value = task;
    isEditModalOpen.value = true;
}

function openDelete(task: Task): void {
    selectedTask.value = task;
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
            <Button label="Нова задача" icon="pi pi-plus" :disabled="!props.projects.length" @click="openCreate" />
        </template>

        <div class="flex h-full flex-col gap-4">
            <div v-if="props.tasks.data.length" class="flex flex-col gap-2">
                <TaskCard
                    v-for="task in props.tasks.data"
                    :key="task.id"
                    :task="task"
                    @edit="openEdit(task)"
                    @delete="openDelete(task)"
                />
            </div>

            <div v-else class="flex flex-col items-center gap-3 rounded-lg border border-dashed border-gray-300 py-16 dark:border-gray-600">
                <i class="pi pi-check-square text-3xl text-gray-500"></i>
                <p class="text-gray-500">Задач поки немає</p>
                <Button
                    v-if="props.projects.length"
                    label="Створити першу задачу"
                    icon="pi pi-plus"
                    text
                    @click="openCreate"
                />
                <p v-else class="text-sm text-gray-500">Спершу створіть проєкт — задача має належати проєкту</p>
            </div>

            <TaskFormModal
                v-model:visible="isEditModalOpen"
                :task="selectedTask"
                :projects="props.projects"
                :assignees="props.assignees"
                @done="selectedTask = null"
                @cancel="selectedTask = null"
            />

            <TaskDeleteModal
                v-model:visible="isDeleteModalOpen"
                :task="selectedTask"
                @done="selectedTask = null"
                @cancel="selectedTask = null"
            />
        </div>
    </AdminLayout>
</template>
