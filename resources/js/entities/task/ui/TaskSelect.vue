<script setup lang="ts">
import { onMounted } from 'vue';
import { useTaskOptions } from '../model/useTaskOptions';
import type { TaskOption } from '../model/types';

const props = withDefaults(defineProps<{
    placeholder?: string;
    invalid?: boolean;
    disabled?: boolean;
    showClear?: boolean;
}>(), {
    placeholder: 'Оберіть задачу',
});

/** The whole option, so callers also get the task's project without a second lookup. */
const model = defineModel<TaskOption | null>({ default: null });

const { tasks, loading, load, search } = useTaskOptions();

onMounted(() => load());
</script>

<template>
    <Select
        v-model="model"
        :options="tasks"
        option-label="title"
        data-key="id"
        filter
        :loading="loading"
        :placeholder="props.placeholder"
        :invalid="props.invalid"
        :disabled="props.disabled"
        :show-clear="props.showClear"
        fluid
        @filter="search($event.value)"
    >
        <template #option="{ option }">
            <div class="flex min-w-0 flex-col">
                <span class="truncate">{{ option.title }}</span>
                <small
                    v-if="option.project"
                    class="truncate text-muted-color"
                >
                    {{ option.project.data.title }}
                </small>
            </div>
        </template>
    </Select>
</template>
