<script setup lang="ts">
import { watch } from 'vue';
import { useTimeEntryUpdate, type TimeEntry } from '@/entities/time-entry';
import { FormDate, FormText } from '@/shared/ui';

const props = defineProps<{
    projectId: number;
    entry: TimeEntry | null;
}>();

const visible = defineModel<boolean>('visible', { required: true });

const { form, reset, submit } = useTimeEntryUpdate();

const pickerProps = {
    dateFormat: 'dd.mm.yy',
    showIcon: true,
    showTime: true,
    hourFormat: '24' as const,
};

// Every opening starts from the entry, not from what was left in a closed dialog.
watch([visible, () => props.entry], ([isVisible, entry]) => {
    if (isVisible && entry) reset(entry);
}, { immediate: true });

function save(): void {
    if (!props.entry) return;

    submit(props.projectId, props.entry, () => {
        visible.value = false;
    });
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        header="Редагувати час"
        :style="{ width: '32rem' }"
        :breakpoints="{ '640px': '95vw' }"
    >
        <div v-if="props.entry" class="flex flex-col gap-4">
            <p v-if="props.entry.task" class="flex items-center gap-1.5 text-sm font-medium">
                <i class="pi pi-check-square shrink-0 text-xs text-primary" title="Задача" />
                <span class="truncate">{{ props.entry.description }}</span>
            </p>
            <FormText
                v-else
                v-model="form.description"
                name="time-entry-description"
                label="Назва"
                :message="form.errors.description"
            />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormDate
                    v-model="form.started_at"
                    name="time-entry-start"
                    label="Початок"
                    :input-props="pickerProps"
                    :message="form.errors.started_at"
                />
                <FormDate
                    v-model="form.stopped_at"
                    name="time-entry-stop"
                    label="Кінець"
                    :input-props="pickerProps"
                    :message="form.errors.stopped_at"
                />
            </div>
        </div>

        <template #footer>
            <Button
                type="button"
                label="Скасувати"
                severity="secondary"
                text
                :disabled="form.processing"
                @click="visible = false"
            />
            <Button
                type="button"
                label="Зберегти"
                :loading="form.processing"
                @click="save"
            />
        </template>
    </Dialog>
</template>
