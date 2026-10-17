<script setup lang="ts">
import Code from '@primeicons/vue/code';
import { type Task, type TaskAssignee } from '@/entities/task';
import { MediaPreview, type Media } from '@/entities/media';
import { TaskUploadFiles } from '@/features/task-upload-files';
import { TaskDescriptionField, TaskTitleField } from '@/features/task-inline-edit';
import { computed, ref } from 'vue';
import { MediaDeleteModal } from '@/features/media-delete';
import { TaskCreateComment } from '@/features/task-create-comment';
import { CommentCard } from '@/entities/comment';
import type { CommentID, CommentIncludes } from '@/entities/comment';
import type { User } from '@/entities/user';
import { CommentDeleteModal } from '@/features/comment-delete';
import { CommentEditForm } from '@/features/comment-edit';
import TaskSidebarForm from './TaskSidebarForm.vue';

defineProps<{
    task: Task | null;
    media: Media[];
    comments: CommentIncludes<User>[];
    assignees: TaskAssignee[];
    canEdit: boolean;
}>();

const selectedMedia = ref<Media | null>(null);
const isMediaModalVisible = computed(() => !!selectedMedia.value);

const selectedEditCommentId = ref<CommentID | null>(null);
const selectedDeleteCommentId = ref<CommentID | null>(null);
const isDeleteCommentModalVisible = computed(() => !!selectedDeleteCommentId.value);

const visible = defineModel<boolean>('visible', { required: true });

defineEmits(['onClose']);
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        header="Задача"
        class="overflow-hidden"
        :pt="{
            root: {
                class: '!max-h-full sm:w-[90%] sm:!max-h-[90%] !rounded-none sm:!rounded-xl',
            },
            content: {
                class: 'overflow-hidden flex-1 flex flex-col !px-4'
            }
        }"
    >
        <div v-if="task" class="grid grid-cols-1 lg:grid-cols-3 gap-2 h-full overflow-hidden">
            <div class="col-span-2 overflow-y-auto px-1 pr-4 flex flex-col gap-2">
                <TaskTitleField :task="task" :disabled="!canEdit" />

                <div class="flex flex-col gap-2 lg:hidden">
                    <TaskSidebarForm :task="task" :assignees="assignees" :disabled="!canEdit" />
                </div>

                <TaskDescriptionField :task="task" :disabled="!canEdit" />

                <TaskUploadFiles :task-id="task.id" />

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
                </div>

                <MediaDeleteModal
                    v-model:visible="isMediaModalVisible"
                    :media-id="selectedMedia?.id ?? 0"
                    :file-name="selectedMedia?.file_name ?? ''"
                    @on-done="selectedMedia = null"
                    @on-cancel="selectedMedia = null"
                />

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
                            <TaskCreateComment :task-id="task.id" />
                            <div class="space-y-4">
                                <CommentCard
                                    v-for="comment in comments" 
                                    :key="comment.id"
                                    :user-name="comment.user.data.name"
                                    :body="comment.body"
                                    :created-at="comment.created_at"
                                    :is-editing-mode="selectedEditCommentId === comment.id"
                                    @on-edit="selectedEditCommentId = comment.id"
                                    @on-delete="selectedDeleteCommentId = comment.id"
                                >
                                    <template #edit-container>
                                        <CommentEditForm
                                            :comment-id="comment.id"
                                            :body="comment.body"
                                            @on-done="selectedEditCommentId = null"
                                            @on-cancel="selectedEditCommentId = null"
                                        />
                                    </template>
                                </CommentCard>
                            </div>
                            <CommentDeleteModal
                                v-model:visible="isDeleteCommentModalVisible"
                                :comment-id="selectedDeleteCommentId ?? 0"
                                @on-done="selectedDeleteCommentId = null"
                                @on-cancel="selectedDeleteCommentId = null"
                            />
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>

            <div class="col-span-1 lg:flex flex-col gap-2 hidden">
                <TaskSidebarForm :task="task" :assignees="assignees" :disabled="!canEdit" />
            </div>
        </div>
        <template #footer>
            <div class="mt-2 flex justify-end gap-2">
                <Button
                    type="button"
                    label="Закрити"
                    severity="secondary"
                    text
                    @click="$emit('onClose')"
                />
            </div>
        </template>
    </Dialog>
</template>