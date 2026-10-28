<script setup lang="ts">
import { computed, onMounted, watch } from 'vue';
import { useTaskOptions } from '../model/useTaskOptions';
import type { TaskSelectOption } from '../model/types';

const props = withDefaults(defineProps<{
    label?: string;
    message?: string;
    placeholder?: string;
    /** Only tasks of this project are offered; nothing is loaded without it. */
    projectId?: number | null;
    disabled?: boolean;
}>(), {
    placeholder: 'Оберіть задачі',
    projectId: null,
});

/** Whole options, so callers also get each task's project without a second lookup. */
const model = defineModel<TaskSelectOption[]>({ default: () => [] });

const { tasks, loading, loadByProject, search } = useTaskOptions({ limit: 50 });

// Selected tasks stay listed (and keep their labels) even when a new search no longer returns them.
const groups = computed(() => {
    const byProject = new Map<string, TaskSelectOption[]>();
    const options = [...model.value, ...tasks.value.filter((t) => !model.value.some((s) => s.id === t.id))];

    for (const task of options) {
        const project = task.project?.data.title ?? 'Без проєкту';
        byProject.set(project, [...(byProject.get(project) ?? []), task]);
    }

    return [...byProject].map(([label, items]) => ({ label, items }));
});

function load(): void {
    if (props.projectId) {
        loadByProject(props.projectId);
    } else {
        tasks.value = [];
    }
}

onMounted(load);

watch(() => props.projectId, load);
</script>

<template>
    <div class="flex flex-col gap-1">
        <Label v-if="props.label">{{ props.label }}</Label>

        <MultiSelect
            v-model="model"
            :options="groups"
            option-label="title"
            option-group-label="label"
            option-group-children="items"
            data-key="id"
            filter
            display="chip"
            :loading="loading"
            :placeholder="props.placeholder"
            :invalid="Boolean(props.message)"
            :disabled="props.disabled"
            :show-toggle-all="false"
            fluid
            @filter="props.projectId && search(props.projectId, $event.value)"
        />

        <Message v-if="props.message" severity="error" size="small" variant="simple">
            {{ props.message }}
        </Message>
    </div>
</template>
