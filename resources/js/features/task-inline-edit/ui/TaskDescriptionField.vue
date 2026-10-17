<script setup lang="ts">
import { computed, ref, toRefs } from 'vue';
import { useTaskFieldForm, type Task } from '@/entities/task';
import { FormEditor } from '@/shared/ui';
import { isBlankHtml } from '@/shared/lib';

const props = defineProps<{
    task: Task;
    disabled?: boolean;
}>();

const { task } = toRefs(props);

const { form, error, save, cancel } = useTaskFieldForm(task, 'description');

const isEditing = ref(false);

// Older tasks may hold an empty editor's markup (<p></p>) instead of null.
const hasDescription = computed(() => !isBlankHtml(task.value.description));

function startEditing(): void {
    if (props.disabled) return;

    isEditing.value = true;
}

function commit(): void {
    if (isBlankHtml(form.value as string | null)) form.value = null;

    save({ onSuccess: () => (isEditing.value = false) });
}

function abort(): void {
    cancel();
    isEditing.value = false;
}
</script>

<template>
    <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <Label>Опис</Label>
            <Button
                v-if="!isEditing && !disabled && hasDescription"
                type="button"
                icon="pi pi-pencil"
                label="Редагувати"
                size="small"
                severity="secondary"
                text
                @click="startEditing"
            />
        </div>

        <template v-if="isEditing">
            <FormEditor
                :model-value="(form.value as string | null) ?? ''"
                name="task-description"
                placeholder="Введіть опис завдання..."
                :message="error"
                @update:model-value="form.value = $event"
            />
            <div class="flex justify-end gap-2">
                <Button
                    type="button"
                    label="Скасувати"
                    severity="secondary"
                    size="small"
                    text
                    :disabled="form.processing"
                    @click="abort"
                />
                <Button
                    type="button"
                    label="Зберегти"
                    size="small"
                    :loading="form.processing"
                    @click="commit"
                />
            </div>
        </template>

        <div
            v-else-if="hasDescription"
            class="rounded-md"
            :class="{ 'cursor-text hover:bg-gray-50 dark:hover:bg-gray-800': !disabled }"
            @click="startEditing"
        >
            <FormEditor :model-value="task.description ?? ''" readonly />
        </div>

        <button
            v-else-if="!disabled"
            type="button"
            class="rounded-md border border-dashed border-gray-300 px-3 py-4 text-left text-sm text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
            @click="startEditing"
        >
            Додати опис…
        </button>

        <p v-else class="px-3 text-sm text-gray-500">Опису немає</p>
    </div>
</template>
