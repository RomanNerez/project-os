<script setup lang="ts">
import { Pagination as PaginationBar } from '@/shared/ui';
import type { Pagination } from '@/shared/types';
import { PROJECTS_PER_PAGE_OPTIONS, useProjectPagination } from '../model/useProjectPagination';

defineProps<{ pagination: Pagination }>();

const { setPage, setPerPage } = useProjectPagination();
</script>

<template>
    <PaginationBar
        :pagination="pagination"
        :rows-per-page-options="PROJECTS_PER_PAGE_OPTIONS"
        @page-change="setPage"
        @rows-change="setPerPage"
    />
</template>
