<script setup lang="ts">
import { computed } from 'vue';
import { STATUS_META, type Project } from '../model/types';

const props = defineProps<{
    project: Project;
}>();

defineEmits<{
    edit: [];
    delete: [];
}>();

const initials = computed(() =>
    props.project.title
        .split(' ')
        .filter(Boolean)
        .map((part) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase(),
);

const activeUntilLabel = computed(() => '10 грудня 2026');

const budgetLabel = computed(() =>
    new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(props.project.budget),
);

const status = computed(() => STATUS_META[props.project.status]);
</script>

<template>
    <Card>
        <template #content>
            <div class="flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <Avatar :label="initials" size="large" shape="circle" class="shrink-0" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold">{{ project.title }}</p>
                        <p class="text-xs text-muted-color">Активний до {{ activeUntilLabel }}</p>
                    </div>
                    <Tag :value="status.label" :severity="status.severity" />
                </div>

                <p class="line-clamp-2 min-h-10 text-sm text-muted-color">{{ project.description }}</p>

                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted-color">Прогрес</span>
                        <span class="font-medium">50%</span>
                    </div>
                    <ProgressBar :value="50" :show-value="false" class="h-2!" />
                </div>

                <div class="flex items-center justify-between border-t border-surface-200 pt-3 dark:border-surface-700">
                    <div class="text-sm">
                        <span class="text-muted-color">Бюджет: </span>
                        <span class="font-semibold">{{ budgetLabel }}</span>
                    </div>
                    <div class="flex gap-1">
                        <Button icon="pi pi-pencil" severity="secondary" text size="small" aria-label="Редагувати" @click="$emit('edit')" />
                        <Button icon="pi pi-trash" severity="danger" text size="small" aria-label="Видалити" @click="$emit('delete')" />
                    </div>
                </div>
            </div>
        </template>
    </Card>
</template>
