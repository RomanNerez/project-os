<script setup lang="ts">
import { computed, ref, toRef, watch } from 'vue';
import FilePdf from '@primeicons/vue/file-pdf';
import ImageIcon from '@primeicons/vue/image';
import Paperclip from '@primeicons/vue/paperclip';
import Times from '@primeicons/vue/times';
import {
    calcAmountUah,
    CURRENCY_OPTIONS,
    FINANCE_CURRENCY,
    formatMoney,
    STATUS_OPTIONS,
    TYPE_OPTIONS,
    useFinanceForm,
    type FinanceWithTasks,
} from '@/entities/finance';
import type { Media } from '@/entities/media';
import { ProjectSelect } from '@/entities/project';
import { TaskMultiSelect, type TaskSelectOption } from '@/entities/task';
import { formatSize, parseDateValue, toDateValue } from '@/shared/lib';
import type { IncludedData } from '@/shared/types';
import { FormDate, FormNumber, FormRadioGroup, FormSelect, FormText, FormTextarea } from '@/shared/ui';

const ACCEPT = 'application/pdf,image/jpeg,image/png,image/webp';

const DATE_INPUT_PROPS = { dateFormat: 'dd.mm.yy', showIcon: true, showButtonBar: true };

const props = defineProps<{
    /** The record to edit; null creates a new one. */
    finance: (FinanceWithTasks & { media: IncludedData<Media[]> }) | null;
    /** Project a new record starts in. */
    projectId?: number | null;
}>();

const emit = defineEmits(['onDone', 'onCancel']);

const visible = defineModel<boolean>('visible', { required: true });

const isEdit = computed(() => props.finance !== null);

const { form, submit, reset } = useFinanceForm(toRef(props, 'finance'), () => {
    emit('onDone');
    clear();
}, props.projectId ?? null);

const selectedTasks = ref<TaskSelectOption[]>([]);
const fileInput = ref<HTMLInputElement | null>(null);

const taskProjectIds = computed(() => (form.project_id ? [form.project_id] : []));

watch(selectedTasks, (tasks) => {
    form.task_ids = tasks.map((task) => task.id);
});

// Tasks must belong to the payment's project, so switching it drops the others.
watch(() => form.project_id, (projectId) => {
    selectedTasks.value = selectedTasks.value.filter((task) => task.project?.data.id === projectId);
});

const dueDate = computed({
    get: () => parseDateValue(form.due_date),
    set: (value: Date | null) => { form.due_date = toDateValue(value); },
});

const paidAt = computed({
    get: () => parseDateValue(form.paid_at),
    set: (value: Date | null) => { form.paid_at = toDateValue(value); },
});

const storedReceipts = computed(() => (props.finance?.media.data ?? [])
    .filter((file) => !form.removed_receipt_ids.includes(file.id)));

const isUsd = computed(() => form.currency === FINANCE_CURRENCY.USD);

const amountUah = computed(() => calcAmountUah(form.amount, form.currency, form.exchange_rate));

const receiptsError = computed(() => {
    const errors = form.errors as Record<string, string | undefined>;

    return errors.receipts ?? Object.entries(errors).find(([key]) => key.startsWith('receipts.'))?.[1];
});

// Every opening starts from the record (or an empty draft), not from what was left in a closed dialog.
watch([visible, () => props.finance], ([isVisible]) => {
    if (isVisible) clear();
}, { immediate: true });

function tasksOf(finance: FinanceWithTasks | null): TaskSelectOption[] {
    if (!finance) return [];

    return finance.tasks.data.map((task) => ({ id: task.id, title: task.title, project: { data: finance.project } }));
}

function addFiles(event: Event): void {
    const input = event.target as HTMLInputElement;

    form.receipts = [...form.receipts, ...Array.from(input.files ?? [])];
    input.value = '';
}

function removeFile(index: number): void {
    form.receipts = form.receipts.filter((_, i) => i !== index);
}

function removeStoredReceipt(id: number): void {
    form.removed_receipt_ids = [...form.removed_receipt_ids, id];
}

function clear(): void {
    reset();
    selectedTasks.value = tasksOf(props.finance);
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
        :style="{ width: '40rem' }"
        :breakpoints="{ '640px': '95vw' }"
    >
        <div class="flex flex-col gap-4">
            <FormText
                v-model="form.title"
                autofocus
                fluid
                label="Назва"
                placeholder="Напр., Оплата верстки лендінгу"
                :message="form.errors.title"
            />

            <FormRadioGroup
                v-model="form.type"
                name="finance-type"
                label="Тип"
                :options="TYPE_OPTIONS"
                :message="form.errors.type"
            />

            <ProjectSelect
                v-model="form.project_id"
                label="Проєкт"
                :selected="props.finance?.project"
                :message="form.errors.project_id"
            />

            <TaskMultiSelect
                v-model="selectedTasks"
                label="Задачі"
                :placeholder="form.project_id ? 'Необов’язково' : 'Спершу оберіть проєкт'"
                :project-ids="taskProjectIds"
                :disabled="!form.project_id"
                :message="form.errors.task_ids"
            />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <FormSelect
                    v-model="form.currency"
                    label="Валюта"
                    :options="CURRENCY_OPTIONS"
                    option-label="label"
                    option-value="value"
                    :message="form.errors.currency"
                />

                <FormNumber
                    v-model="form.amount"
                    name="finance-amount"
                    label="Сума"
                    :min="0"
                    :min-fraction-digits="2"
                    :max-fraction-digits="2"
                    locale="uk-UA"
                    :message="form.errors.amount"
                />

                <FormNumber
                    v-if="isUsd"
                    v-model="form.exchange_rate"
                    name="finance-rate"
                    label="Курс, ₴ за $1"
                    :min="0"
                    :min-fraction-digits="2"
                    :max-fraction-digits="4"
                    locale="uk-UA"
                    :message="form.errors.exchange_rate"
                />
            </div>

            <div
                v-if="isUsd"
                class="rounded-lg bg-surface-100 px-3 py-2 text-sm dark:bg-surface-800"
            >
                <span class="text-muted-color">У гривнях:</span>
                <span class="ml-1 font-semibold">
                    {{ amountUah === null ? 'вкажіть суму та курс' : `≈ ${formatMoney(amountUah, FINANCE_CURRENCY.UAH)}` }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <FormSelect
                    v-model="form.status"
                    label="Статус"
                    :options="STATUS_OPTIONS"
                    option-label="label"
                    option-value="value"
                    :message="form.errors.status"
                />
                <FormDate
                    v-model="dueDate"
                    name="finance-due-date"
                    label="Термін оплати"
                    placeholder="дд.мм.рррр"
                    :input-props="DATE_INPUT_PROPS"
                    :message="form.errors.due_date"
                />
                <FormDate
                    v-model="paidAt"
                    name="finance-paid-at"
                    label="Дата оплати"
                    placeholder="дд.мм.рррр"
                    :input-props="DATE_INPUT_PROPS"
                    :message="form.errors.paid_at"
                />
            </div>

            <FormTextarea
                v-model="form.description"
                label="Опис"
                placeholder="Коментар до платежу"
                :rows="3"
                :message="form.errors.description"
            />

            <div class="flex flex-col gap-2">
                <Label>Квитанції</Label>
                <input
                    ref="fileInput"
                    type="file"
                    class="hidden"
                    multiple
                    :accept="ACCEPT"
                    @change="addFiles"
                >
                <div>
                    <Button type="button" severity="secondary" variant="outlined" size="small" @click="fileInput?.click()">
                        <Paperclip />
                        Додати файл
                    </Button>
                    <small class="ml-2 text-muted-color">PDF, JPG, PNG, WEBP · до 10 МБ</small>
                </div>
                <ul v-if="storedReceipts.length" class="flex flex-col gap-1">
                    <li
                        v-for="file in storedReceipts"
                        :key="file.id"
                        class="flex items-center justify-between gap-2 rounded-md border border-surface-200 px-3 py-1.5 text-sm dark:border-surface-700"
                    >
                        <a
                            :href="file.origin_url"
                            target="_blank"
                            rel="noopener"
                            class="flex min-w-0 items-center gap-1 text-primary hover:underline"
                        >
                            <FilePdf v-if="file.mime_type === 'application/pdf'" class="shrink-0" />
                            <ImageIcon v-else class="shrink-0" />
                            <span class="truncate">{{ file.file_name }}</span>
                        </a>
                        <span class="flex shrink-0 items-center gap-2 text-muted-color">
                            {{ formatSize(file.size) }}
                            <Button
                                type="button"
                                text
                                rounded
                                size="small"
                                severity="secondary"
                                aria-label="Видалити квитанцію"
                                @click="removeStoredReceipt(file.id)"
                            >
                                <Times />
                            </Button>
                        </span>
                    </li>
                </ul>
                <ul v-if="form.receipts.length" class="flex flex-col gap-1">
                    <li
                        v-for="(file, index) in form.receipts"
                        :key="`${file.name}-${index}`"
                        class="flex items-center justify-between gap-2 rounded-md border border-surface-200 px-3 py-1.5 text-sm dark:border-surface-700"
                    >
                        <span class="truncate">{{ file.name }}</span>
                        <span class="flex shrink-0 items-center gap-2 text-muted-color">
                            {{ formatSize(file.size) }}
                            <Button
                                type="button"
                                text
                                rounded
                                size="small"
                                severity="secondary"
                                aria-label="Прибрати файл"
                                @click="removeFile(index)"
                            >
                                <Times />
                            </Button>
                        </span>
                    </li>
                </ul>
                <Message v-if="receiptsError" severity="error" size="small" variant="simple">
                    {{ receiptsError }}
                </Message>
            </div>
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
                    @click="submit"
                />
            </div>
        </template>
    </Dialog>
</template>
