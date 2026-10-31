<script setup lang="ts">
import { computed, ref } from 'vue';
import { roleLabel, type RoleID, type WorkspaceRole } from '@/entities/role';
import { RoleFormModal } from '@/features/role-form';
import { RoleDeleteModal } from '@/features/role-delete';
import { EmptyList } from '@/shared/ui';

const props = defineProps<{
    roles: WorkspaceRole[];
}>();

const isFormOpen = ref(false);
const isDeleteOpen = ref(false);
const selectedRoleId = ref<RoleID | null>(null);
const selectedRole = computed(() => props.roles.find((role) => role.id === selectedRoleId.value) ?? null);

function openCreate(): void {
    selectedRoleId.value = null;
    isFormOpen.value = true;
}

function openEdit(id: RoleID): void {
    selectedRoleId.value = id;
    isFormOpen.value = true;
}

function openDelete(id: RoleID): void {
    selectedRoleId.value = id;
    isDeleteOpen.value = true;
}

function clearSelection(): void {
    selectedRoleId.value = null;
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold">Ролі робочого простору</h2>
                <p class="text-sm text-muted-color">
                    Роль призначається користувачу в розділі «Команда». Дозволи ролей налаштовуються у вкладці «Доступи».
                </p>
            </div>
            <Button label="Нова роль" icon="pi pi-plus" @click="openCreate" />
        </div>

        <DataTable v-if="roles.length" :value="roles" data-key="id">
            <Column header="Роль" class="min-w-48">
                <template #body="{ data }">
                    <span class="font-medium">{{ roleLabel(data) }}</span>
                </template>
            </Column>
            <Column header="Опис">
                <template #body="{ data }">
                    <span v-if="data.description" class="text-sm">{{ data.description }}</span>
                    <span v-else class="text-sm text-muted-color">—</span>
                </template>
            </Column>
            <Column class="w-0">
                <template #body="{ data }">
                    <div class="flex justify-end gap-1">
                        <Button
                            icon="pi pi-pencil"
                            severity="secondary"
                            text
                            rounded
                            aria-label="Редагувати"
                            @click="openEdit(data.id)"
                        />
                        <Button
                            icon="pi pi-trash"
                            severity="danger"
                            text
                            rounded
                            aria-label="Видалити"
                            @click="openDelete(data.id)"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>

        <EmptyList
            v-else
            icon-class="pi-shield"
            decription="Ролей поки немає"
            button-label="Створити першу роль"
            @on-handler="openCreate"
        />

        <RoleFormModal
            v-model:visible="isFormOpen"
            :role="selectedRole"
            @done="clearSelection"
            @cancel="clearSelection"
        />

        <RoleDeleteModal
            v-model:visible="isDeleteOpen"
            :role="selectedRole"
            @done="clearSelection"
            @cancel="clearSelection"
        />
    </div>
</template>
