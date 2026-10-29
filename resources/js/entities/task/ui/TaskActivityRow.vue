<script setup lang="ts">
import { computed } from 'vue';
import { UserAvatar } from '@/shared/ui';
import { formatDateHuman, formatEstimate } from '@/shared/lib';
import {
    ACTIVITY_FIELD_LABEL,
    PRIORITY_META,
    STATUS_META,
    type TaskActivity,
    type TaskActivityChange,
    type TaskPriority,
    type TaskStatus,
} from '../model/types';

const props = defineProps<{
    userName: string;
    userAvatar: string | null;
    activity: TaskActivity;
}>();

type ChangeValue = TaskActivityChange['old'];

interface ValueBadge {
    label: string;
    severity: string;
    icon?: string;
}

/** Status and priority read better as the same badges the task cards use. */
function badgeFor(field: TaskActivityChange['field'], value: ChangeValue): ValueBadge | null {
    if (value === null) return null;

    if (field === 'status') return STATUS_META[value as TaskStatus] ?? null;
    if (field === 'priority') return PRIORITY_META[value as TaskPriority] ?? null;

    return null;
}

function formatValue(field: TaskActivityChange['field'], value: ChangeValue): string {
    if (value === null || value === '') {
        return field === 'assignee' ? 'Не призначено' : '—';
    }

    if (field === 'estimate') {
        return Number(value) > 0 ? formatEstimate(Number(value)) : '—';
    }

    return String(value);
}

const changes = computed(() => props.activity.changes.map((change) => ({
    field: change.field,
    label: ACTIVITY_FIELD_LABEL[change.field],
    values: [change.old, change.new].map((value) => ({
        badge: badgeFor(change.field, value),
        text: formatValue(change.field, value),
    })),
})));
</script>

<template>
    <!-- One line from sm up; on phones the changes wrap under the author. -->
    <div class="flex flex-col gap-1 rounded-lg border border-gray-200 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-800 md:flex-row md:items-center md:gap-3">
        <div class="flex min-w-0 shrink-0 items-center gap-2">
            <UserAvatar :name="userName" :image="userAvatar" class="!size-6 shrink-0 !text-xs" />
            <span class="truncate text-sm font-medium">{{ userName }}</span>
            <span class="shrink-0 text-xs text-gray-400">• {{ formatDateHuman(activity.created_at) }}</span>
        </div>

        <div class="min-w-0 flex-1">
            <p v-if="activity.event === 'created'" class="text-sm text-gray-600 dark:text-gray-300">
                <i class="pi pi-plus-circle mr-1 text-xs text-gray-400" />
                Створив(ла) задачу
            </p>

            <ul v-else class="flex flex-wrap items-center gap-x-4 gap-y-1">
                <li
                    v-for="change in changes"
                    :key="change.field"
                    class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-600 dark:text-gray-300"
                >
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ change.label }}:</span>
                    <!-- The description is rich text; its before/after would be unreadable inline. -->
                    <span v-if="change.field === 'description'">оновлено</span>
                    <template v-for="(value, index) in change.values" v-else :key="index">
                        <i v-if="index === 1" class="pi pi-arrow-right text-xs text-gray-400" />
                        <Tag
                            v-if="value.badge"
                            :value="value.badge.label"
                            :icon="value.badge.icon"
                            :severity="value.badge.severity"
                            class="!px-1.5 !py-0.5 !text-xs"
                            :class="{ 'opacity-60': index === 0 }"
                        />
                        <span
                            v-else
                            :class="index === 0
                                ? 'text-gray-400 line-through'
                                : 'font-medium text-gray-900 dark:text-gray-100'"
                        >{{ value.text }}</span>
                    </template>
                </li>
            </ul>
        </div>
    </div>
</template>
