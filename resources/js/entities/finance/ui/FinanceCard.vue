<script setup lang="ts">
import FilePdf from '@primeicons/vue/file-pdf';
import ImageIcon from '@primeicons/vue/image';
import type { FinanceCardFile } from '../model/types';

const props = withDefaults(defineProps<{
    title: string;
    /** Already formatted, e.g. "+41 500,00 ₴". */
    amount: string;
    /** The original-currency amount for non-UAH records, e.g. "1 000,00 $ × 41.5". */
    originalAmount?: string;
    isIncome?: boolean;
    typeLabel?: string;
    typeColor?: string;
    statusLabel?: string;
    statusColor?: string;
    description?: string | null;
    projects?: string[];
    tasks?: string[];
    dueDate?: string | null;
    paidAt?: string | null;
    files?: FinanceCardFile[];
}>(), {
    isIncome: false,
    typeColor: 'secondary',
    statusColor: 'secondary',
    projects: () => [],
    tasks: () => [],
    files: () => [],
});
</script>

<template>
    <article
        class="flex flex-col gap-3 rounded-xl border border-surface-200 bg-surface-0 p-4 sm:flex-row sm:items-start sm:justify-between dark:border-surface-700 dark:bg-surface-900"
    >
        <div class="flex min-w-0 flex-col gap-2">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="truncate font-semibold">{{ props.title }}</h3>
                <Tag v-if="props.typeLabel" :value="props.typeLabel" :severity="props.typeColor" />
                <Tag v-if="props.statusLabel" :value="props.statusLabel" :severity="props.statusColor" />
            </div>

            <div v-if="props.projects.length || props.tasks.length" class="flex flex-wrap gap-1 text-sm">
                <Tag
                    v-for="(project, index) in props.projects"
                    :key="`p-${index}`"
                    :value="project"
                    severity="secondary"
                    icon="pi pi-folder"
                />
                <Tag
                    v-for="(task, index) in props.tasks"
                    :key="`t-${index}`"
                    :value="task"
                    severity="contrast"
                    icon="pi pi-check-square"
                />
            </div>

            <p v-if="props.description" class="line-clamp-2 text-sm text-muted-color">
                {{ props.description }}
            </p>

            <div v-if="props.dueDate || props.paidAt" class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-color">
                <span v-if="props.dueDate">Термін: {{ props.dueDate }}</span>
                <span v-if="props.paidAt">Оплачено: {{ props.paidAt }}</span>
            </div>

            <div v-if="props.files.length" class="flex flex-wrap gap-2">
                <a
                    v-for="file in props.files"
                    :key="file.id"
                    :href="file.url"
                    target="_blank"
                    rel="noopener"
                    class="flex items-center gap-1 text-xs text-primary hover:underline"
                >
                    <FilePdf v-if="file.isPdf" />
                    <ImageIcon v-else />
                    {{ file.name }}
                </a>
            </div>
        </div>

        <div class="flex shrink-0 flex-col sm:items-end">
            <span
                class="text-lg font-semibold"
                :class="props.isIncome ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
            >
                {{ props.amount }}
            </span>
            <span v-if="props.originalAmount" class="text-xs text-muted-color">
                {{ props.originalAmount }}
            </span>
        </div>
    </article>
</template>
