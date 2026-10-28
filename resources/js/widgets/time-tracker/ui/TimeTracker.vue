<script setup lang="ts">
import { computed, ref, toRef, watch } from 'vue';
import { formatDuration } from '@/shared/lib';
import {
    TIME_TRACKER_MODE,
    TIME_TRACKER_MODE_OPTIONS,
    useElapsedSeconds,
    useTimeTracker,
    type TimeEntry,
} from '@/entities/time-entry';
import { TaskSelect, type TaskOption } from '@/entities/task';

const props = defineProps<{
    running: TimeEntry | null;
    serverTime: string;
    projectId: number;
}>();

const { form, mode, start, stop } = useTimeTracker();

const isRunning = computed(() => props.running !== null);

const displayedMode = computed(() => {
    if (props.running === null) {
        return mode.value;
    }

    return props.running.task ? TIME_TRACKER_MODE.TASK : TIME_TRACKER_MODE.FREE;
});

const isTaskMode = computed(() => displayedMode.value === TIME_TRACKER_MODE.TASK);

const selectedTask = ref<TaskOption | null>(null);

watch(selectedTask, (task) => {
    form.task_id = task?.id ?? null;
});

watch(() => form.task_id, (id) => {
    if (id === null) {
        selectedTask.value = null;
    }
});

watch(mode, () => form.clearErrors());

const elapsed = useElapsedSeconds(
    computed(() => props.running?.started_at ?? null),
    toRef(props, 'serverTime'),
);

function submit(): void {
    isRunning.value ? stop(props.projectId) : start(props.projectId);
}
</script>

<template>
    <form
        class="flex flex-col gap-3 rounded-lg border border-[var(--p-content-border-color)] bg-[var(--p-content-background)] p-4 shadow-sm"
        @submit.prevent="submit"
    >
        <SelectButton
            :model-value="displayedMode"
            :options="TIME_TRACKER_MODE_OPTIONS"
            option-label="label"
            option-value="value"
            :allow-empty="false"
            :disabled="isRunning"
            size="small"
            class="self-start"
            @update:model-value="mode = $event"
        />

        <div class="flex flex-col gap-3 lg:flex-row lg:items-start">
            <div class="min-w-0 flex-1">
                <InputText
                    v-if="isRunning"
                    :model-value="running!.description"
                    readonly
                    fluid
                />
                <template v-else-if="isTaskMode">
                    <TaskSelect
                        v-model="selectedTask"
                        :invalid="Boolean(form.errors.task_id)"
                        :project-id="projectId"
                        show-clear
                    />
                    <small v-if="form.errors.task_id" class="text-red-500">{{ form.errors.task_id }}</small>
                </template>
                <template v-else>
                    <InputText
                        v-model="form.description"
                        placeholder="Над чим працюєте?"
                        :invalid="Boolean(form.errors.description)"
                        autofocus
                        fluid
                    />
                    <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
                </template>
            </div>

            <div class="flex items-center justify-between gap-3 lg:justify-end">
                <span
                    class="font-mono text-xl tabular-nums"
                    :class="isRunning ? 'text-primary' : 'text-muted-color'"
                >
                    {{ formatDuration(elapsed) }}
                </span>

                <Button
                    type="submit"
                    :label="isRunning ? 'Стоп' : 'Старт'"
                    :icon="isRunning ? 'pi pi-stop-circle' : 'pi pi-play-circle'"
                    :severity="isRunning ? 'danger' : 'primary'"
                    :loading="form.processing"
                    class="w-28 shrink-0"
                />
            </div>
        </div>
    </form>
</template>
