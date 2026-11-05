<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import {
    FinanceTaskHoursCard,
    FinanceTaskMultiSelect,
    paymentsOfTask,
    toHoursUsers,
    withTaskPayments,
    type FinanceTaskHours,
    type FinanceTaskHoursChange,
    type FinanceTaskPayment,
} from '@/entities/finance';

const props = withDefaults(defineProps<{
    projectId: number;
    /** The record being edited: its time is offered again and its tasks are preselected. */
    exceptFinanceId?: number | null;
    preselectTaskIds?: number[];
    errors?: Record<string, string | undefined>;
    disabled?: boolean;
}>(), {
    exceptFinanceId: null,
    preselectTaskIds: () => [],
    errors: () => ({}),
});

/** Selected tasks with their unpaid hours; the parent needs them for the rates. */
const tasks = defineModel<FinanceTaskHours[]>('tasks', { required: true });

/** Seconds paid per (task, user), returned to the parent. */
const payments = defineModel<FinanceTaskPayment[]>('payments', { required: true });

const preselected = ref(false);

const paymentsError = computed(() => Object.entries(props.errors)
    .find(([key]) => key.startsWith('task_payments'))?.[1]);

// A task taken out of the selection takes its payments with it.
watch(tasks, (selected) => {
    const ids = new Set(selected.map((item) => item.task.id));

    if (payments.value.some((payment) => !ids.has(payment.task_id))) {
        payments.value = payments.value.filter((payment) => ids.has(payment.task_id));
    }
});

function preselect(loaded: FinanceTaskHours[]): void {
    if (preselected.value) return;

    preselected.value = true;

    if (props.preselectTaskIds.length && !tasks.value.length) {
        tasks.value = loaded.filter((item) => props.preselectTaskIds.includes(item.task.id));
    }
}

function setPayment(taskId: number, { user_id, seconds }: FinanceTaskHoursChange): void {
    const byUser = { ...paymentsOfTask(payments.value, taskId), [user_id]: seconds };

    payments.value = withTaskPayments(payments.value, taskId, byUser);
}

function removeTask(taskId: number): void {
    tasks.value = tasks.value.filter((item) => item.task.id !== taskId);
}
</script>

<template>
    <section class="flex flex-col gap-4">
        <FinanceTaskMultiSelect
            v-model="tasks"
            label="Задачі до оплати"
            placeholder="Необов’язково"
            :project-id="props.projectId"
            :except-finance-id="props.exceptFinanceId"
            :disabled="props.disabled"
            :message="props.errors.task_ids"
            @loaded="preselect"
        />

        <p v-if="!tasks.length" class="text-sm text-muted-color">
            Додайте задачі, щоб оплатити затреканий по них неоплачений час.
        </p>

        <FinanceTaskHoursCard
            v-for="item in tasks"
            :key="item.task.id"
            :title="item.task.title"
            :users="toHoursUsers(item.users)"
            :disabled="props.disabled"
            :model-value="paymentsOfTask(payments, item.task.id)"
            @update:model-value="setPayment(item.task.id, $event)"
            @remove="removeTask(item.task.id)"
        />

        <Message v-if="paymentsError" severity="error" size="small" variant="simple">
            {{ paymentsError }}
        </Message>
    </section>
</template>
