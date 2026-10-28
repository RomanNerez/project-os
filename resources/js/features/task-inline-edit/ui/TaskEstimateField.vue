<script setup lang="ts">
import { computed, toRefs } from 'vue';
import { useTaskFieldForm, type Task } from '@/entities/task';
import { FormNumber } from '@/shared/ui';

const props = defineProps<{
    task: Task;
    disabled?: boolean;
}>();

const { task } = toRefs(props);

const { form, error, save } = useTaskFieldForm(task, 'estimate', { revertOnError: true });

// Stored in minutes, entered in hours; an empty or zero value means "not estimated".
const hours = computed({
    get: () => (form.value === null ? null : (form.value as number) / 60),
    set: (value: number | null) => {
        form.value = value === null || value <= 0 ? null : Math.round(value * 60);
    },
});
</script>

<template>
    <FormNumber
        v-model="hours"
        name="task-estimate"
        label="Оцінка, год"
        placeholder="Не оцінено"
        :min="0"
        :max="1000"
        :max-fraction-digits="2"
        :disabled="disabled || form.processing"
        :message="error"
        @focusout="save()"
        @keydown.enter.prevent="save()"
    />
</template>
