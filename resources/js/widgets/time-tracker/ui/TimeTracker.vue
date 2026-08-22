<script setup lang="ts">
import { computed, toRef } from 'vue';
import { formatDuration } from '@/shared/lib';
import {
    useElapsedSeconds,
    useTimeTracker,
    type TimeEntry,
    type TimeEntryProject,
} from '@/entities/time-entry';

const props = defineProps<{
    running: TimeEntry | null;
    projects: TimeEntryProject[];
    serverTime: string;
}>();

const { form, start, stop } = useTimeTracker();

const isRunning = computed(() => props.running !== null);

const elapsed = useElapsedSeconds(
    computed(() => props.running?.started_at ?? null),
    toRef(props, 'serverTime'),
);

function submit(): void {
    isRunning.value ? stop() : start();
}
</script>

<template>
    <form
        class="flex flex-col gap-3 rounded-lg border border-[var(--p-content-border-color)] bg-[var(--p-content-background)] p-4 shadow-sm lg:flex-row lg:items-center"
        @submit.prevent="submit"
    >
        <div class="min-w-0 flex-1">
            <InputText
                v-if="isRunning"
                :model-value="running!.description"
                readonly
                fluid
            />
            <InputText
                v-else
                v-model="form.description"
                placeholder="Над чим працюєте?"
                autofocus
                fluid
            />
            <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
        </div>

        <div class="w-full lg:w-56">
            <Select
                v-if="isRunning"
                :model-value="running!.project?.id ?? null"
                :options="projects"
                option-label="title"
                option-value="id"
                placeholder="Без проєкту"
                disabled
                fluid
            />
            <Select
                v-else
                v-model="form.project_id"
                :options="projects"
                option-label="title"
                option-value="id"
                placeholder="Без проєкту"
                show-clear
                fluid
            />
            <small v-if="form.errors.project_id" class="text-red-500">{{ form.errors.project_id }}</small>
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
    </form>
</template>
