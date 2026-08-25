<script setup lang="ts">
import { toRefs } from 'vue';
import { STATUS_OPTIONS, useTaskForm, type Task, type TaskAssignee, type TaskProject } from '@/entities/task';
import Comments from './Comments.vue';
import Code from '@primeicons/vue/code';
import { FormEditor, FormSelect, FormText } from '@/shared/ui';
import UploadFiles from './UploadFiles.vue';

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
        header="Редагувати задачу"
        class="overflow-hidden"
        :style="{ width: '70%' }"
        :pt="{ content: { class: 'overflow-hidden flex-1 flex flex-col !px-4' } }"
    >
        <form class="grid grid-cols-3 gap-2 h-full overflow-hidden">
            <div class="col-span-2 overflow-y-auto px-1 pr-4 flex flex-col gap-2">
                <FormText
                    v-model="form.title"
                    autofocus
                    fluid
                    label="Назва"
                    placeholder="Назва задачі"
                    :message="form.errors.title"
                />

                <FormEditor
                    v-model="form.description"
                    name="task-description"
                    label="Опис"
                    placeholder="Введіть опис завдання..."
                    :message="form.errors.description"
                />

                <UploadFiles />

                <Tabs value="tab1">
                    <TabList>
                        <Tab value="tab1" class="flex items-center gap-2!">
                            <Code />
                            Коментарі
                        </Tab>
                    </TabList>
                    <TabPanels
                        :pt="{ root: { class: '!px-0' } }"
                    >
                        <TabPanel value="tab1" >
                            <Comments />
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>

            <div class="col-span-1 flex flex-col gap-2">
                <FormSelect
                    v-model="form.project_id"
                    name="task-project"
                    label="Проєкт"
                    :options="projects"
                    option-label="title"
                    option-value="id"
                    placeholder="Оберіть проєкт"
                    :message="form.errors.project_id"
                />

                <FormSelect
                    v-model="form.assignee_id"
                    name="task-assignee"
                    label="Виконавець"
                    :options="assignees"
                    option-label="name"
                    option-value="id"
                    placeholder="Не призначено"
                    show-clear
                    :message="form.errors.assignee_id"
                />

                <FormSelect
                    v-model="form.status"
                    name="task-status"
                    label="Статус"
                    :options="STATUS_OPTIONS"
                    option-label="label"
                    option-value="value"
                    :message="form.errors.status"
                />
            </div>
        </form>
        <template #footer>
            <div class="mt-2 flex justify-end gap-2">
                <Button type="button" label="Скасувати" severity="secondary" text :disabled="form.processing" @click="cancel" />
                <Button type="button" label="Зберегти" :loading="form.processing" @click="submit" />
            </div>
        </template>
    </Dialog>
</template>
