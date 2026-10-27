<script setup lang="ts">
import { computed } from 'vue';
import { formatDateHuman } from '@/shared/lib';
import type { AppNotification } from '../model/types';

const props = defineProps<{
    notification: AppNotification;
}>();

defineEmits<{
    select: [notification: AppNotification];
}>();

const unread = computed(() => !props.notification.read_at);
</script>

<template>
    <button
        type="button"
        class="flex w-full cursor-pointer gap-3 rounded-md px-3 py-2 text-left hover:bg-surface-100 dark:hover:bg-surface-800"
        :class="{ 'bg-primary-50 dark:bg-primary-950/40': unread }"
        @click="$emit('select', notification)"
    >
        <span
            class="mt-1.5 size-2 shrink-0 rounded-full"
            :class="unread ? 'bg-primary' : 'bg-transparent'"
        ></span>
        <span class="flex min-w-0 flex-1 flex-col gap-0.5">
            <span class="text-sm" :class="{ 'font-semibold': unread }">{{ notification.title }}</span>
            <span v-if="notification.message" class="text-sm text-muted-color">{{ notification.message }}</span>
            <span class="text-xs text-muted-color">{{ formatDateHuman(notification.created_at) }}</span>
        </span>
    </button>
</template>
