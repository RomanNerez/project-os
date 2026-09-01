<script setup lang="ts">
    import { ROLES, STATUSES, type AiChatMessageRole, type AiChatMessageStatus } from '../model/types';

    interface Props {
        role: AiChatMessageRole;
        status: AiChatMessageStatus;
        content: string;  
    }

    defineProps<Props>();
</script>

<template>
    <div
        class="max-w-[85%] p-3 text-sm leading-relaxed rounded-2xl"
        :class="[
            status === STATUSES.FAILED
                ? 'bg-red-100 dark:bg-red-950/50 text-red-700 dark:text-red-300 border border-red-300 dark:border-red-800 rounded-bl-none'
                : role === ROLES.USER
                    ? 'bg-[var(--p-primary-color)] text-[var(--p-primary-contrast-color)] rounded-br-none'
                    : 'bg-[var(--p-surface-100)] dark:bg-[var(--p-surface-800)] text-[var(--p-surface-900)] dark:text-[var(--p-surface-0)] rounded-bl-none'
        ]"
    >
        {{ status === STATUSES.FAILED ? 'Упс, я спіймав помилку...' : content }}
    </div>
</template>
