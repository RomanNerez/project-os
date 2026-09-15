<script setup lang="ts">
import { STATUS_OPTIONS, type ProjectStatus } from '@/entities/project';
import { FormText, FormMultiSelect } from '@/shared/ui';
import { useProjectFilters } from '../model/useProjectFilters';

const { filters, isActive, setSearch, setStatuses, reset } = useProjectFilters();
</script>

<template>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <FormText
            name="project-search"
            placeholder="Пошук за назвою"
            class="w-full sm:w-72"
            :model-value="filters.search"
            @update:model-value="setSearch(String($event))"
        />

        <FormMultiSelect
            name="project-statuses"
            placeholder="Усі статуси"
            class="w-full sm:w-64"
            option-label="label"
            option-value="value"
            :options="STATUS_OPTIONS"
            :model-value="filters.statuses"
            :show-toggle-all="false"
            @update:model-value="setStatuses($event as ProjectStatus[])"
        />

        <Button
            label="Скинути фільтри"
            icon="pi pi-filter-slash"
            severity="secondary"
            text
            :disabled="!isActive"
            @click="reset"
        />
    </div>
</template>
