<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import Paperclip from '@primeicons/vue/paperclip';
import Times from '@primeicons/vue/times';
import {
    calcAmountUah,
    CURRENCY_OPTIONS,
    FINANCE_CURRENCY,
    formatMoney,
    STATUS_OPTIONS,
    TYPE_OPTIONS,
    useCreateFinance,
} from '@/entities/finance';
import { useProjectOptions } from '@/entities/project';
import { TaskMultiSelect, type TaskOption } from '@/entities/task';
import { formatSize } from '@/shared/lib';
import { FormDate, FormRadioGroup, FormSelect, FormText, FormTextarea } from '@/shared/ui';

const ACCEPT = 'application/pdf,image/jpeg,image/png,image/webp';

const emit = defineEmits(['onDone', 'onCancel']);

const visible = defineModel<boolean>('visible', { required: true });

const { form, submit, reset } = useCreateFinance(() => {
    emit('onDone');
    clear();
});

const { projects, loading: projectsLoading, load: loadProjects, search: searchProjects } = useProjectOptions();

/** Only narrows the task list; the payment's projects are the projects of its tasks. */
const projectFilter = ref<number[]>([]);
const selectedTasks = ref<TaskOption[]>([]);
const fileInput = ref<HTMLInputElement | null>(null);

watch(selectedTasks, (tasks) => {
    form.task_ids = tasks.map((task) => task.id);
});

const taskProjects = computed(() => [
    ...new Map(selectedTasks.value.flatMap((t) => (t.project ? [[t.project.data.id, t.project.data.title]] : []))).values(),
]);

const isUsd = computed(() => form.currency === FINANCE_CURRENCY.USD);

const amountUah = computed(() => calcAmountUah(form.amount, form.currency, form.exchange_rate));

const receiptsError = computed(() => {
    const errors = form.errors as Record<string, string | undefined>;

    return errors.receipts ?? Object.entries(errors).find(([key]) => key.startsWith('receipts.'))?.[1];
});

onMounted(() => loadProjects());

function addFiles(event: Event): void {
    const input = event.target as HTMLInputElement;

    form.receipts = [...form.receipts, ...Array.from(input.files ?? [])];
    input.value = '';
}

function removeFile(index: number): void {
    form.receipts = form.receipts.filter((_, i) => i !== index);
}

function clear(): void {
    reset();
    projectFilter.value = [];
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
        header="Новий фінансовий запис"
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

            <div class="flex flex-col gap-1">
                <Label for="finance-projects">Проєкти</Label>
                <MultiSelect
                    v-model="projectFilter"
                    input-id="finance-projects"
                    placeholder="Усі проєкти"
                    :options="projects"
                    option-label="title"
                    option-value="id"
                    filter
                    display="chip"
                    :loading="projectsLoading"
                    :show-toggle-all="false"
                    fluid
                    @filter="searchProjects($event.value)"
                />
            </div>

            <div class="flex flex-col gap-1">
                <TaskMultiSelect
                    v-model="selectedTasks"
                    label="Задачі"
                    :project-ids="projectFilter"
                    :message="form.errors.task_ids"
                />
                <small v-if="taskProjects.length" class="text-muted-color">
                    Проєкти: {{ taskProjects.join(', ') }}
                </small>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <FormSelect
                    v-model="form.currency"
                    label="Валюта"
                    :options="CURRENCY_OPTIONS"
                    option-label="label"
                    option-value="value"
                    :message="form.errors.currency"
                />

                <div class="flex flex-col gap-1">
                    <Label for="finance-amount">Сума</Label>
                    <InputNumber
                        v-model="form.amount"
                        input-id="finance-amount"
                        :min="0"
                        :min-fraction-digits="2"
                        :max-fraction-digits="2"
                        locale="uk-UA"
                        :invalid="Boolean(form.errors.amount)"
                        fluid
                    />
                    <Message v-if="form.errors.amount" severity="error" size="small" variant="simple">
                        {{ form.errors.amount }}
                    </Message>
                </div>

                <div v-if="isUsd" class="flex flex-col gap-1">
                    <Label for="finance-rate">Курс, ₴ за $1</Label>
                    <InputNumber
                        v-model="form.exchange_rate"
                        input-id="finance-rate"
                        :min="0"
                        :min-fraction-digits="2"
                        :max-fraction-digits="4"
                        locale="uk-UA"
                        :invalid="Boolean(form.errors.exchange_rate)"
                        fluid
                    />
                    <Message v-if="form.errors.exchange_rate" severity="error" size="small" variant="simple">
                        {{ form.errors.exchange_rate }}
                    </Message>
                </div>
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
                    v-model="form.due_date"
                    label="Термін оплати"
                    placeholder="дд.мм.рррр"
                    :input-props="{ dateFormat: 'dd.mm.yy', showIcon: true, showButtonBar: true }"
                    :message="form.errors.due_date"
                />
                <FormDate
                    v-model="form.paid_at"
                    label="Дата оплати"
                    placeholder="дд.мм.рррр"
                    :input-props="{ dateFormat: 'dd.mm.yy', showIcon: true, showButtonBar: true }"
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
                <Button type="button" label="Створити" :loading="form.processing" @click="submit" />
            </div>
        </template>
    </Dialog>
</template>
