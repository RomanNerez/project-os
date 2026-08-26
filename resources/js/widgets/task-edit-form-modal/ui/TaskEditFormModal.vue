<script setup lang="ts">
import Code from '@primeicons/vue/code';
import { STATUS_OPTIONS, useTaskForm, type Task, type TaskAssignee, type TaskProject } from '@/entities/task';
import { MediaPreview, type Media } from '@/entities/media';
import { TaskUploadFiles } from '@/features/task-upload-files';
import { FormEditor, FormSelect, FormText } from '@/shared/ui';
import { computed, ref, toRefs } from 'vue';
import { MediaDeleteModal } from '@/features/media-delete';

const props = defineProps<{
    task: Task | null;
    media: Media[];
    projects: TaskProject[];
    assignees: TaskAssignee[];
}>();

const selectedMedia = ref<Media | null>(null);
const isMediaModalVisible = computed(() => !!selectedMedia.value);

const visible = defineModel<boolean>('visible', { required: true });

const emit = defineEmits<{
    done: [];
    cancel: [];
}>();

const { task } = toRefs(props);

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
        :style="{ width: '90%' }"
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

                <TaskUploadFiles
                    v-if="task?.id"
                    :task-id="task?.id"
                />

                <div v-if="media.length > 0" >
                    <div class="grid grid-flow-col auto-cols-[120px] gap-2 overflow-x-auto w-full">
                        <MediaPreview
                            v-for="(m, i) of media" 
                            :key="m.id"
                            :file-name="m.file_name"
                            :url="m.preview_url"
                            :size="m.size"
                            @on-remove="selectedMedia = m"
                        />
                    </div>

                    <MediaDeleteModal
                        v-model:visible="isMediaModalVisible"
                        :media-id="selectedMedia?.id ?? 0"
                        :file-name="selectedMedia?.file_name ?? ''"
                        @done="selectedMedia = null"
                        @cancel="selectedMedia = null"
                    />
                </div>

                <!-- <Tabs value="tab1">
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
                </Tabs> -->
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