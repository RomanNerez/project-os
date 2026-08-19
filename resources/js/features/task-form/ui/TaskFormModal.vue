<script setup lang="ts">
import { toRefs, watch } from 'vue';
import { STATUS_OPTIONS, useTaskForm, type Task, type TaskAssignee, type TaskProject } from '@/entities/task';

const props = defineProps<{
    task: Task | null;
    projects: TaskProject[];
    assignees: TaskAssignee[];
}>();

const emit = defineEmits<{
    done: [];
    cancel: [];
}>();

const { task } = toRefs(props);

const visible = defineModel<boolean>('visible', { required: true });

const { form, submit, reset } = useTaskForm(
    task,
    () => {
        emit('done');
        visible.value = false;
        reset()
    },
);

function cancel() {
    emit('cancel');
    visible.value = false;
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :header="task ? 'Редагувати задачу' : 'Нова задача'"
        class="w-full max-w-lg"
    >
        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="flex flex-col gap-2">
                <label for="task-title" class="text-sm font-medium">Назва</label>
                <InputText id="task-title" v-model="form.title" placeholder="Назва задачі" autofocus fluid />
                <small v-if="form.errors.title" class="text-red-500">{{ form.errors.title }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="task-description" class="text-sm font-medium">Опис</label>
                <Textarea id="task-description" v-model="form.description" rows="3" placeholder="Короткий опис задачі" auto-resize fluid />
                <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="task-project" class="text-sm font-medium">Проєкт</label>
                <Select
                    v-model="form.project_id"
                    label-id="task-project"
                    :options="projects"
                    option-label="title"
                    option-value="id"
                    placeholder="Оберіть проєкт"
                    fluid
                />
                <small v-if="form.errors.project_id" class="text-red-500">{{ form.errors.project_id }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="task-assignee" class="text-sm font-medium">Виконавець</label>
                <Select
                    v-model="form.assignee_id"
                    label-id="task-assignee"
                    :options="assignees"
                    option-label="name"
                    option-value="id"
                    placeholder="Не призначено"
                    show-clear
                    fluid
                />
                <small v-if="form.errors.assignee_id" class="text-red-500">{{ form.errors.assignee_id }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="task-status" class="text-sm font-medium">Статус</label>
                <Select
                    v-model="form.status"
                    label-id="task-status"
                    :options="STATUS_OPTIONS"
                    option-label="label"
                    option-value="value"
                    fluid
                />
                <small v-if="form.errors.status" class="text-red-500">{{ form.errors.status }}</small>
            </div>

            <div class="mt-2 flex justify-end gap-2">
                <Button type="button" label="Скасувати" severity="secondary" text :disabled="form.processing" @click="cancel" />
                <Button type="submit" :label="task ? 'Зберегти' : 'Створити'" :loading="form.processing" />
            </div>
        </form>
    </Dialog>
</template>
