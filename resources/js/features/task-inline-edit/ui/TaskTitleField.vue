<script setup lang="ts">
import { ref, toRefs } from 'vue';
import { useTaskFieldForm, type Task } from '@/entities/task';
import { FormTextarea } from '@/shared/ui';

const props = defineProps<{
    task: Task;
    disabled?: boolean;
}>();

const { task } = toRefs(props);

const { form, error, save, cancel } = useTaskFieldForm(task, 'title');

const isEditing = ref(false);

function startEditing(): void {
    if (props.disabled) return;

    isEditing.value = true;
}

function commit(): void {
    if (!isEditing.value) return;

    save({ onSuccess: () => (isEditing.value = false) });
}

function abort(): void {
    cancel();
    isEditing.value = false;
}
</script>

<template>
    <!-- A title has no line breaks: Enter saves, long titles just wrap. -->
    <FormTextarea
        v-if="isEditing"
        :model-value="form.value as string"
        name="task-title"
        placeholder="Назва задачі"
        :rows="1"
        auto-resize
        :message="error"
        :textarea-props="{ autofocus: true, class: '!text-xl !font-semibold resize-none' }"
        @update:model-value="form.value = $event.replace(/\n/g, ' ')"
        @keydown.enter.prevent="commit"
        @keydown.esc.stop.prevent="abort"
        @focusout="commit"
    />

    <h2
        v-else
        class="group flex items-center gap-2 rounded-md px-3 py-2 text-xl font-semibold"
        :class="{ 'cursor-text hover:bg-gray-100 dark:hover:bg-gray-800': !disabled }"
        :role="disabled ? undefined : 'button'"
        :tabindex="disabled ? undefined : 0"
        @click="startEditing"
        @keydown.enter.prevent="startEditing"
    >
        <span class="min-w-0 break-words">{{ task.title }}</span>
        <i
            v-if="!disabled"
            class="pi pi-pencil text-sm text-gray-400 opacity-0 transition-opacity group-hover:opacity-100"
        />
    </h2>
</template>
