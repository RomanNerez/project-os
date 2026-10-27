<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import type { DataTablePageEvent } from 'primevue/datatable';
import { WorkspaceLayout } from '@/widgets/workspace-layout';
import { userRoutes, useResendInvitation, type UserInvitation, type UserListItem } from '@/entities/user';
import { UserCreateFormModal } from '@/features/user-create-form';
import { UserInviteFormModal } from '@/features/user-invite-form';
import { formatDateLong } from '@/shared/lib';
import type { PaginatedServerData, ServerData } from '@/shared/types';
import { UserAvatar } from '@/shared/ui';

const props = defineProps<{
    users: PaginatedServerData<UserListItem[]>;
    invitations: ServerData<UserInvitation[]>;
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

const invitations = computed(() => props.invitations.data.map((invitation) => ({
    id: invitation.id,
    email: invitation.email,
    invitedBy: invitation.invited_by_name,
    expiresAt: formatDateLong(invitation.expires_at),
})));

const { resend, resendingId } = useResendInvitation();
// Resend swaps the invitation for a new one, so the confirmation is keyed by email, not id.
const resentEmail = ref<string | null>(null);
const resendError = ref<{ email: string; message: string } | null>(null);

function resendInvitation(id: number, email: string): void {
    resendError.value = null;
    resend(id, {
        onSuccess: () => { resentEmail.value = email; },
        onError: (errors) => {
            resendError.value = { email, message: errors.email ?? 'Не вдалося надіслати запрошення' };
        },
    });
}

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

        <div class="flex h-full flex-col gap-4">
            <section
                v-if="invitations.length"
                class="shrink-0 rounded-border border border-surface"
            >
                <h2 class="border-b border-surface px-4 py-3 text-sm font-semibold">
                    Очікують реєстрації ({{ invitations.length }})
                </h2>

                <ul class="max-h-64 divide-y divide-surface overflow-y-auto">
                    <li
                        v-for="invitation in invitations"
                        :key="invitation.id"
                        class="flex flex-wrap items-center gap-3 px-4 py-3"
                    >
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-emphasis text-muted-color">
                            <i class="pi pi-envelope" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium">{{ invitation.email }}</p>
                            <p class="text-sm text-muted-color">
                                <template v-if="invitation.invitedBy">Запросив: {{ invitation.invitedBy }} · </template>
                                Діє до {{ invitation.expiresAt }}
                            </p>
                            <p v-if="resendError?.email === invitation.email" class="text-sm text-red-500">
                                {{ resendError.message }}
                            </p>
                        </div>

                        <Button
                            v-if="resentEmail === invitation.email"
                            label="Надіслано"
                            icon="pi pi-check"
                            severity="success"
                            size="small"
                            text
                            disabled
                        />
                        <Button
                            v-else
                            label="Надіслати ще раз"
                            icon="pi pi-refresh"
                            size="small"
                            outlined
                            :loading="resendingId === invitation.id"
                            :disabled="resendingId !== null && resendingId !== invitation.id"
                            @click="resendInvitation(invitation.id, invitation.email)"
                        />
                    </li>
                </ul>
            </section>

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
                class="min-h-0 flex-1"
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
        </div>

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
