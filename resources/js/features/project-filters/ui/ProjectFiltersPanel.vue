<script setup lang="ts">
import { STATUS_OPTIONS } from '@/entities/project';
import { FormText, FormSelect } from '@/shared/ui';
import { useProjectFilters } from '../model/useProjectFilters';

const { filters, isActive, setSearch, setStatuses, reset } = useProjectFilters();
</script>

<template>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <FormText
            name="project-search"
            placeholder="Пошук за назвою"
            class="w-full sm:w-72"
            :model-value="filters.title"
            @update:model-value="setSearch(String($event))"
        />

        <FormSelect
            name="task-status"
            class="w-full sm:w-64"
            :options="STATUS_OPTIONS"
            option-label="label"
            option-value="value"
            placeholder="Статус"
            multiple
            checkmark
            :model-value="filters.status"
            @update:model-value="setStatuses($event)"
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
