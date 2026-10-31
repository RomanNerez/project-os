<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    roleLabel,
    roleRoutes,
    useToggleRolePermission,
    type RolePermission,
    type WorkspaceRole,
} from '@/entities/role';
import { EmptyList } from '@/shared/ui';

defineProps<{
    roles: WorkspaceRole[];
    permissions: RolePermission[];
}>();

const { toggle, pendingKey } = useToggleRolePermission();

function cellKey(role: WorkspaceRole, permission: RolePermission): string {
    return `${role.id}:${permission.name}`;
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="font-semibold">Доступи ролей</h2>
            <p class="text-sm text-muted-color">
                Позначте, які дозволи має кожна роль. Зміни зберігаються одразу.
            </p>
        </div>

        <DataTable v-if="roles.length" :value="permissions" data-key="name" scrollable>
            <Column header="Дозвіл" frozen class="min-w-56">
                <template #body="{ data }">
                    <span class="font-medium">{{ data.display_name }}</span>
                </template>
            </Column>

            <!-- The system role has every permission and is not editable. -->
            <Column class="min-w-36" :pt="{ columnHeaderContent: { class: 'justify-center' } }">
                <template #header>
                    <span class="font-semibold">Адміністратор</span>
                </template>
                <template #body>
                    <div class="flex justify-center">
                        <Checkbox :model-value="true" binary disabled />
                    </div>
                </template>
            </Column>

            <Column
                v-for="role in roles"
                :key="role.id"
                class="min-w-40"
                :pt="{ columnHeaderContent: { class: 'justify-center' } }"
            >
                <template #header>
                    <span class="font-semibold" :title="role.description ?? undefined">{{ roleLabel(role) }}</span>
                </template>
                <template #body="{ data }">
                    <div class="flex justify-center">
                        <i v-if="pendingKey === cellKey(role, data)" class="pi pi-spin pi-spinner text-muted-color" />
                        <Checkbox
                            v-else
                            :model-value="role.permissions.includes(data.name)"
                            binary
                            :disabled="pendingKey !== null"
                            :aria-label="`${roleLabel(role)}: ${data.display_name}`"
                            @update:model-value="toggle(role, data.name, !!$event)"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>

        <EmptyList
            v-else
            icon-class="pi-shield"
            decription="Спершу створіть роль, щоб налаштувати її доступи"
            button-label="До ролей"
            @on-handler="router.visit(roleRoutes.index())"
        />
    </div>
</template>
