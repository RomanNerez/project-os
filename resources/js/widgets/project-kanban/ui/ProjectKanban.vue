<script setup lang="ts">
import { ref, watch } from 'vue';
import draggable from 'vuedraggable';
import {
    STATUS_META,
    STATUS_OPTIONS,
    TaskKanbanCard,
    updateTaskStatus,
    type Task,
    type TaskStatus,
} from '@/entities/task';

interface Props {
    tasks: Task[];
}

const props = defineProps<Props>();

type Columns = Record<TaskStatus, Task[]>;

function groupByStatus(tasks: Task[]): Columns {
    const grouped = Object.fromEntries(
        STATUS_OPTIONS.map((option) => [option.value, [] as Task[]]),
    ) as Columns;

    tasks.forEach((task) => grouped[task.status]?.push(task));

    return grouped;
}

const columns = ref<Columns>(groupByStatus(props.tasks));

watch(() => props.tasks, (tasks) => {
    columns.value = groupByStatus(tasks);
});

interface DraggableChange {
    added?: { element: Task };
}

function onChange(event: DraggableChange, status: TaskStatus): void {
    if (!event.added) return;

    updateTaskStatus(event.added.element.id, status);
}
</script>

<template>
    <div class="flex flex-1 gap-4 overflow-x-auto pb-5 pl-1 pt-1">
        <div
            v-for="status in STATUS_OPTIONS"
            :key="status.value"
            class="flex w-[300px] shrink-0 flex-col rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900"
        >
            <div class="mb-3 flex items-center gap-2 border-b border-gray-200 pb-2 dark:border-gray-700">
                <Tag
                    :value="status.label"
                    :severity="STATUS_META[status.value].severity"
                    class="text-sm font-semibold"
                />
                <span class="text-xs font-bold text-gray-500">
                    {{ columns[status.value].length }}
                </span>
            </div>

            <draggable
                :list="columns[status.value]"
                group="kanban-tasks"
                item-key="id"
                animation="200"
                ghost-class="opacity-40"
                drag-class="rotate-1"
                class="flex min-h-[120px] flex-1 flex-col gap-3 overflow-y-auto"
                @change="(event: DraggableChange) => onChange(event, status.value)"
            >
                <template #item="{ element: task }: { element: Task }">
                    <TaskKanbanCard :task="task" />
                </template>
            </draggable>

            <p
                v-if="!columns[status.value].length"
                class="pt-2 text-center text-xs text-gray-400"
            >
                Перетягніть задачу сюди
            </p>
        </div>
    </div>
</template>
