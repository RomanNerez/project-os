<script setup lang="ts">
import { computed, ref } from 'vue';
import { ProjectDeleteMemberModal } from '@/features/project-delete-member';
import { getRoleLabel, getRoleSeverity } from '@/entities/project';
import type { ProjectID, ProjectMember } from '@/entities/project';
import { UserAvatar } from '@/shared/ui';

interface Props {
    projectId: ProjectID;
    members: ProjectMember[];
    canManage?: boolean;
}

const props = withDefaults(defineProps<Props>(), { canManage: false });

const selectedMember = ref<ProjectMember | null>(null);
const isOpenDeleteMemberModal = computed(() => !!selectedMember.value);
</script>

<template>
    <div class="overflow-hidden rounded-lg border border-[var(--p-content-border-color)] bg-[var(--p-content-background)] shadow-sm">
        <DataTable :value="props.members" data-key="id">
            <template #empty>Учасників поки немає</template>

            <Column header="Користувач">
                <template #body="{ data }">
                    <div class="flex items-center gap-3">
                        <UserAvatar :name="data.name" />
                        <span class="font-medium">{{ data.name }}</span>
                    </div>
                </template>
            </Column>
            <Column field="email" header="Email" />
            <Column header="Роль">
                <template #body="{ data }">
                    <Tag :value="getRoleLabel(data.role)" :severity="getRoleSeverity(data.role)" />
                </template>
            </Column>
            <Column v-if="props.canManage" header="Дії" class="w-px text-right">
                <template #body="{ data }">
                    <Button
                        icon="pi pi-trash"
                        severity="danger"
                        text
                        rounded
                        size="small"
                        aria-label="Видалити"
                        @click="selectedMember = data"
                    />
                </template>
            </Column>
        </DataTable>

        <ProjectDeleteMemberModal
            v-model:visible="isOpenDeleteMemberModal"
            :project-id="projectId"
            :member-id="selectedMember?.id ?? 0"
            :name="selectedMember?.name ?? ''"
            @on-cancel="selectedMember = null"
            @on-done="selectedMember = null"
        />
    </div>
</template>
