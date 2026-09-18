<script setup lang="ts">
import { computed } from 'vue';
import PrimePaginator, { type PageState } from 'primevue/paginator';
import type { Pagination } from '@/shared/types';

interface Props {
    pagination: Pagination;
    rowsPerPageOptions?: number[];
}

const props = withDefaults(defineProps<Props>(), {
    rowsPerPageOptions: () => [10, 20, 50],
});

const emit = defineEmits<{
    (e: 'pageChange', page: number): void
    (e: 'rowsChange', rows: number): void
}>();

// A page number past the last one comes back from the server as requested, so the
// offset is clamped - otherwise Paginator renders a page that does not exist.
const currentPage = computed(
    () => Math.min(props.pagination.current_page, Math.max(props.pagination.total_pages, 1))
);

const first = computed(() => (currentPage.value - 1) * props.pagination.per_page);

function onPage(event: PageState): void {
    // Paginator reports a page size change through this same event.
    if (event.rows !== props.pagination.per_page) {
        emit('rowsChange', event.rows);

        return;
    }

    const page = event.page + 1;

    if (page !== currentPage.value) emit('pageChange', page);
}
</script>

<template>
    <PrimePaginator
        :first="first"
        :rows="pagination.per_page"
        :total-records="pagination.total"
        :rows-per-page-options="rowsPerPageOptions"
        @page="onPage"
    />
</template>
