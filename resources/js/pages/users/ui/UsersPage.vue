<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import type { DataTablePageEvent } from 'primevue/datatable';
import { WorkspaceLayout } from '@/widgets/workspace-layout';
import { userRoutes, type UserListItem } from '@/entities/user';
import { UserCreateFormModal } from '@/features/user-create-form';
import { UserInviteFormModal } from '@/features/user-invite-form';
import { formatDateLong } from '@/shared/lib';
import type { PaginatedServerData } from '@/shared/types';
import { UserAvatar } from '@/shared/ui';

const props = defineProps<{
    users: PaginatedServerData<UserListItem[]>;
}>();

const isFormOpen = ref(false);
const isInviteOpen = ref(false);
const isLoading = ref(false);

const pagination = computed(() => props.users.meta.pagination);

const rows = computed(() => props.users.data.map((user) => {
    // spatie keeps a copy of each role per guard (web, api), so the same role arrives twice.
    const roles = [...new Map(user.roles.data.map((role) => [role.name, role.display_name ?? role.name])).values()];

    return {
        id: user.id,
        name: user.name,
        email: user.email,
        avatarUrl: user.avatar_url,
        roles: roles.length ? roles : ['Користувач'],
        createdAt: formatDateLong(user.created_at),
    };
}));

function onPage(event: DataTablePageEvent): void {
    router.get(userRoutes.index(), { page: event.page + 1, limit: event.rows }, {
        only: ['users'],
        preserveState: true,
        preserveScroll: true,
        onStart: () => { isLoading.value = true; },
        onFinish: () => { isLoading.value = false; },
    });
}

function closeForm(): void {
    isFormOpen.value = false;
}

function closeInvite(): void {
    isInviteOpen.value = false;
}
</script>

<template>
    <Head title="Команда" />

    <WorkspaceLayout
        active="team"
        description="Користувачі робочого простору та їхні ролі"
    >
        <template #actions>
            <Button label="Запросити" icon="pi pi-envelope" outlined @click="isInviteOpen = true" />
            <Button label="Новий користувач" icon="pi pi-plus" @click="isFormOpen = true" />
        </template>

        <DataTable
            :value="rows"
            data-key="id"
            lazy
            paginator
            :rows="pagination.per_page"
            :first="(pagination.current_page - 1) * pagination.per_page"
            :total-records="pagination.total"
            :rows-per-page-options="[10, 20, 50]"
            :loading="isLoading"
            scrollable
            scroll-height="flex"
            class="h-full"
            @page="onPage"
        >
            <template #empty>
                <p class="py-8 text-center text-muted-color">Користувачів поки немає</p>
            </template>

            <Column header="Користувач">
                <template #body="{ data }">
                    <div class="flex items-center gap-3">
                        <UserAvatar :name="data.name" :image="data.avatarUrl" />
                        <span class="font-medium">{{ data.name }}</span>
                    </div>
                </template>
            </Column>
            <Column field="email" header="Email" />
            <Column header="Роль">
                <template #body="{ data }">
                    <div class="flex flex-wrap gap-1">
                        <Tag v-for="role in data.roles" :key="role" :value="role" severity="secondary" />
                    </div>
                </template>
            </Column>
            <Column field="createdAt" header="Створено" />
        </DataTable>

        <UserCreateFormModal
            v-model:visible="isFormOpen"
            @on-done="closeForm"
            @on-cancel="closeForm"
        />

        <UserInviteFormModal
            v-model:visible="isInviteOpen"
            @on-done="closeInvite"
            @on-cancel="closeInvite"
        />
    </WorkspaceLayout>
</template>
