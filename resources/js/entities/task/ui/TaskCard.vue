<script setup lang="ts">
import { UserAvatar } from '@/entities/user';

const props = withDefaults(defineProps<{
    title: string;
    filesCount?: number;
    commentsCount?: number;
    statusLabel?: string;
    statusColor?: string;
    assigneeName?: string;
    projectTitle?: string;
}>(), {
    filesCount: 0,
    commentsCount: 0,
    statusColor: 'secondary',
});

defineEmits<{
    edit: [];
    delete: [];
}>();
</script>

<template>
    <div class="flex flex-col gap-3 justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm sm:flex-row sm:items-center dark:border-gray-700 dark:bg-gray-800">
        <div class="min-w-0 flex justify-items-start flex-col gap-2">
            <p class="sm:truncate text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ title }}
            </p>
            <div v-if="projectTitle || assigneeName || filesCount || commentsCount" class="flex gap-2 text-xs text-gray-500 dark:text-gray-400">
                <div v-if="projectTitle || assigneeName" class="flex items-center justify-items-end justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <Tag
                        v-if="projectTitle"
                        :value="projectTitle"
                        severity="secondary"
                        class="text-[10px]"
                    />

                    <div v-if="assigneeName" class="flex items-center gap-1">
                        <UserAvatar :name="assigneeName" />
                        <span class="truncate max-w-[120px]">{{ assigneeName }}</span>
                    </div>
                </div>

                <div v-if="filesCount || commentsCount" class="flex gap-2">
                    <div v-if="filesCount" class="flex items-center gap-1">
                        <i class="pi pi-paperclip text-[11px]" />
                        <span>{{ filesCount }}</span>
                    </div>

                    <div v-if="commentsCount" class="flex items-center gap-1">
                        <i class="pi pi-comments text-[11px]" />
                        <span>{{ commentsCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center shrink-0 justify-items-end justify-between gap-2 border-t border-gray-100 pt-2 sm:justify-end sm:border-0 sm:pt-0 dark:border-gray-750">
            <Tag
                v-if="statusLabel"
                :value="statusLabel"
                :severity="statusColor"
                class="shrink-0"
            />

            <div class="flex shrink-0 gap-1">
                <Button 
                    icon="pi pi-pencil" 
                    severity="secondary" 
                    text 
                    size="small" 
                    aria-label="Редагувати" 
                    @click="$emit('edit')" 
                />
                <Button 
                    icon="pi pi-trash" 
                    severity="danger" 
                    text 
                    size="small" 
                    aria-label="Видалити" 
                    @click="$emit('delete')" 
                />
            </div>
        </div>
    </div>
</template>