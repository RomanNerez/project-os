<script setup lang="ts">
import { computed, onMounted, watch } from 'vue';
import { useFinanceTaskOptions } from '../model/useFinanceTaskOptions';
import type { FinanceTaskHours } from '../model/types';

const props = withDefaults(defineProps<{
    projectId: number;
    /** The record being edited: its own payments do not count as paid. */
    exceptFinanceId?: number | null;
    label?: string;
    message?: string;
    placeholder?: string;
    disabled?: boolean;
}>(), {
    exceptFinanceId: null,
    placeholder: 'Оберіть задачі',
});

const emit = defineEmits<{
    /** Tasks returned by the first (unfiltered) load. */
    loaded: [tasks: FinanceTaskHours[]];
}>();

const model = defineModel<FinanceTaskHours[]>({ default: () => [] });

const { tasks, loading, load, search } = useFinanceTaskOptions({ limit: 50 });

// Selected tasks stay listed (and keep their hours) even when a new search no longer returns them.
const options = computed(() => [
    ...model.value,
    ...tasks.value.filter((option) => !model.value.some((selected) => selected.task.id === option.task.id)),
]);

async function reload(): Promise<void> {
    emit('loaded', await load(props.projectId, '', props.exceptFinanceId));
}

onMounted(reload);

watch(() => [props.projectId, props.exceptFinanceId], reload);
</script>

<template>
    <div class="flex flex-col gap-1">
        <Label v-if="props.label">{{ props.label }}</Label>

        <MultiSelect
            v-model="model"
            :options="options"
            option-label="task.title"
            data-key="task.id"
            filter
            display="chip"
            empty-message="Немає задач з неоплаченим часом"
            :loading="loading"
            :placeholder="props.placeholder"
            :invalid="Boolean(props.message)"
            :disabled="props.disabled"
            :show-toggle-all="false"
            fluid
            @filter="search(props.projectId, $event.value, props.exceptFinanceId)"
        />

        <Message v-if="props.message" severity="error" size="small" variant="simple">
            {{ props.message }}
        </Message>
    </div>
</template>
