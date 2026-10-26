<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/shared/types';

defineProps<{
    title?: string;
    description?: string;
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <div
        v-if="title || description || breadcrumbs?.length || $slots.actions"
        class="shrink-0 rounded-lg border border-[var(--p-content-border-color)] bg-[var(--p-content-background)] px-4 py-4 shadow-sm"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 v-if="title" class="text-2xl font-semibold">{{ title }}</h1>
                <Breadcrumb v-if="breadcrumbs?.length" :model="breadcrumbs" class="mb-2 !p-0">
                    <template #item="{ item, props: itemProps }">
                        <Link v-if="item.href" :href="item.href" v-bind="itemProps.action">
                            <span>{{ item.label }}</span>
                        </Link>
                        <span v-else v-bind="itemProps.action">{{ item.label }}</span>
                    </template>
                </Breadcrumb>
                <p v-if="description" class="mt-1 text-sm text-muted-color">{{ description }}</p>
            </div>
            <div v-if="$slots.actions" class="flex items-center gap-2">
                <slot name="actions"></slot>
            </div>
        </div>
    </div>
</template>
