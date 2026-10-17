<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, InfiniteScroll } from '@inertiajs/vue3';
import { AdminLayout } from '@/widgets/admin-layout';
import {
    FINANCE_CURRENCY,
    FINANCE_TYPE,
    FinanceCard,
    formatMoney,
    STATUS_META,
    TYPE_META,
} from '@/entities/finance';
import { FinanceCreateFormModal } from '@/features/finance-create-form';
import { formatDateLong } from '@/shared/lib';
import type { PaginatedServerData } from '@/shared/types';
import { EmptyList } from '@/shared/ui';
import type { FinanceProp } from '../model/types';

const props = defineProps<{
    finances: PaginatedServerData<FinanceProp[]>;
}>();

const isCreateModalOpen = ref(false);

const cards = computed(() => props.finances.data.map((finance) => {
    const isIncome = finance.type === FINANCE_TYPE.INCOME;

    return {
        id: finance.id,
        title: finance.title,
        isIncome,
        amount: `${isIncome ? '+' : '−'}${formatMoney(finance.amount_uah, FINANCE_CURRENCY.UAH)}`,
        originalAmount: finance.currency === FINANCE_CURRENCY.UAH
            ? undefined
            : `${formatMoney(finance.amount, finance.currency)} × ${Number(finance.exchange_rate)}`,
        typeLabel: TYPE_META[finance.type].label,
        typeColor: TYPE_META[finance.type].severity,
        statusLabel: STATUS_META[finance.status].label,
        statusColor: STATUS_META[finance.status].severity,
        description: finance.description,
        projects: finance.projects.map((project) => project.title),
        tasks: finance.tasks.data.map((task) => task.title),
        dueDate: formatDateLong(finance.due_date),
        paidAt: formatDateLong(finance.paid_at),
        files: finance.media.data.map((file) => ({
            id: file.id,
            name: file.file_name,
            url: file.origin_url,
            isPdf: file.mime_type === 'application/pdf',
        })),
    };
}));

function openCreate(): void {
    isCreateModalOpen.value = true;
}

function closeCreate(): void {
    isCreateModalOpen.value = false;
}
</script>

<template>
    <Head title="Фінанси" />

    <AdminLayout
        title="Фінанси"
        description="Доходи та витрати за задачами проєктів"
    >
        <template #actions>
            <Button label="Новий запис" icon="pi pi-plus" @click="openCreate" />
        </template>

        <div class="flex h-full flex-col gap-4">
            <div v-if="cards.length" class="min-h-0 flex-1 overflow-y-auto">
                <InfiniteScroll class="flex flex-col gap-2" data="finances">
                    <FinanceCard
                        v-for="card in cards"
                        :key="card.id"
                        :title="card.title"
                        :amount="card.amount"
                        :original-amount="card.originalAmount"
                        :is-income="card.isIncome"
                        :type-label="card.typeLabel"
                        :type-color="card.typeColor"
                        :status-label="card.statusLabel"
                        :status-color="card.statusColor"
                        :description="card.description"
                        :projects="card.projects"
                        :tasks="card.tasks"
                        :due-date="card.dueDate"
                        :paid-at="card.paidAt"
                        :files="card.files"
                    />
                </InfiniteScroll>
            </div>

            <EmptyList
                v-else
                icon-class="pi-wallet"
                decription="Фінансових записів поки немає"
                button-label="Створити перший запис"
                :show-action="true"
                @on-handler="openCreate"
            />

            <FinanceCreateFormModal
                v-model:visible="isCreateModalOpen"
                @on-done="closeCreate"
                @on-cancel="closeCreate"
            />
        </div>
    </AdminLayout>
</template>
