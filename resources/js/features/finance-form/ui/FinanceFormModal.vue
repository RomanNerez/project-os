<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import {
    calcMinAmount,
    calcPaymentsUsd,
    formatMoney,
    useFinanceForm,
    type FinanceIncludes,
    type FinanceTask,
    type FinanceTaskHours,
    type FinanceTaskPayment,
} from '@/entities/finance';
import type { Media } from '@/entities/media';
import type { Project } from '@/entities/project';
import FinanceMainFields from './FinanceMainFields.vue';
import FinanceTasksSection from './FinanceTasksSection.vue';

const props = defineProps<{
    finance: FinanceIncludes<Project, FinanceTask[], Media[]> | null;
    projectId: number;
}>();
const emit = defineEmits(['onDone', 'onCancel']);

const visible = defineModel<boolean>('visible', { required: true });

const isEdit = computed(() => props.finance !== null);

const { form, reset, create, update } = useFinanceForm();

const selectedTasks = ref<FinanceTaskHours[]>([]);

const payments = computed<FinanceTaskPayment[]>({
    get: () => form.task_payments,
    set: (value) => { form.task_payments = value; },
});

const preselectTaskIds = computed(() => props.finance?.tasks.data.map((task) => task.id) ?? []);

const hasPayments = computed(() => form.task_payments.some((payment) => payment.seconds > 0));

/** The hours cost in the record's currency; `null` while the UAH rate is missing. */
const minAmount = computed(() => {
    if (!hasPayments.value) return null;

    const hoursByTask = new Map(selectedTasks.value.map((item) => [item.task.id, item]));

    return calcMinAmount(calcPaymentsUsd(form.task_payments, hoursByTask), form.currency, form.exchange_rate);
});

watch(selectedTasks, (tasks) => {
    form.task_ids = tasks.map((item) => item.task.id);
});

watch(visible, (isVisible) => {
    if (isVisible) clear();
}, { immediate: true });

function save(): void {
    form.clearErrors('amount', 'exchange_rate');

    if (!form.exchange_rate) {
        form.setError('exchange_rate', 'Вкажіть курс долара.');
        return;
    }

    if (minAmount.value !== null && (form.amount ?? 0) < minAmount.value) {
        form.setError('amount', `Сума не може бути меншою за оплату годин: ${formatMoney(minAmount.value, form.currency)}.`);
        return;
    }

    const done = () => emit('onDone');

    if (props.finance) {
        update(props.projectId, props.finance.id, done);
    } else {
        create(props.projectId, done);
    }
}

function clear(): void {
    reset(props.finance);
    selectedTasks.value = [];
}

function cancel(): void {
    clear();
    emit('onCancel');
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :header="isEdit ? 'Редагувати запис' : 'Новий фінансовий запис'"
        :style="{ width: '72rem' }"
        :breakpoints="{ '1200px': '95vw' }"
    >
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <FinanceMainFields
                :form="form"
                :media="props.finance?.media.data ?? []"
                :has-payments="hasPayments"
                :min-amount="minAmount"
            />

            <FinanceTasksSection
                v-model:tasks="selectedTasks"
                v-model:payments="payments"
                :project-id="props.projectId"
                :except-finance-id="props.finance?.id ?? null"
                :preselect-task-ids="preselectTaskIds"
                :errors="form.errors"
                :disabled="form.processing"
            />
        </div>

        <template #footer>
            <div class="mt-2 flex justify-end gap-2">
                <Button
                    type="button"
                    label="Скасувати"
                    severity="secondary"
                    text
                    :disabled="form.processing"
                    @click="cancel"
                />
                <Button
                    type="button"
                    :label="isEdit ? 'Зберегти' : 'Створити'"
                    :loading="form.processing"
                    @click="save"
                />
            </div>
        </template>
    </Dialog>
</template>
