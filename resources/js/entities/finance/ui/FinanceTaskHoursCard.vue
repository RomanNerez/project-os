<script setup lang="ts">
import Times from '@primeicons/vue/times';
import FinanceUserHoursItem from './FinanceUserHoursItem.vue';
import type { FinanceTaskHoursChange, FinanceTaskHoursUser } from '../model/types';

const props = defineProps<{
    title: string;
    users: FinanceTaskHoursUser[];
    modelValue: Record<number, number>;
    disabled?: boolean;
    message?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [change: FinanceTaskHoursChange];
    remove: [];
}>();
</script>

<template>
    <article class="flex flex-col gap-3 rounded-lg border border-surface-200 p-3 dark:border-surface-700">
        <header class="flex items-center justify-between gap-2">
            <h4 class="truncate text-sm font-semibold">{{ props.title }}</h4>
            <Button
                type="button"
                text
                rounded
                size="small"
                severity="secondary"
                aria-label="Прибрати задачу"
                :disabled="props.disabled"
                @click="emit('remove')"
            >
                <Times />
            </Button>
        </header>

        <p v-if="!props.users.length" class="text-sm text-muted-color">
            По задачі ще немає затреканого часу.
        </p>

        <ul v-else class="flex flex-col gap-4">
            <li v-for="user in props.users" :key="user.id">
                <FinanceUserHoursItem
                    :name="user.name"
                    :avatar_url="user.avatar_url"
                    :total-seconds="user.seconds"
                    :disabled="props.disabled"
                    :model-value="props.modelValue[user.id] ?? 0"
                    @update:model-value="emit('update:modelValue', { user_id: user.id, seconds: $event })"
                />
            </li>
        </ul>

        <Message v-if="props.message" severity="error" size="small" variant="simple">
            {{ props.message }}
        </Message>
    </article>
</template>
