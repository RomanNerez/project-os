<script setup lang="ts">
import Dialog from 'primevue/dialog';
import { ProjectAddMemberForm } from '@/features/project-add-member';
import { MemberItem, PROJECT_OWNER_ROLE } from '@/entities/project';
import type { ProjectMember } from '@/entities/project';
import { ProjectDeleteMemberModal } from '@/features/project-delete-member';
import { computed, ref } from 'vue';

interface Owner {
  id: number;
  name: string;
  email: string;
}

interface Props {
  visible: boolean;
  projectId: number;
  owner?: Owner;
  members?: ProjectMember[];
}

const props = defineProps<Props>();

const selectedMember = ref<ProjectMember | null>(null)
const isOpenDeleteMemeberModal = computed(() => !!selectedMember.value);

defineEmits<{
  (e: 'update:visible', value: boolean): void;
}>();
</script>

<template>
  <Dialog
    :visible="props.visible"
    modal
    header="Управління командою проєкту"
    :style="{ width: '32rem' }"
    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    @update:visible="$emit('update:visible', false)"
  >
    <div class="flex flex-col gap-6 pt-2">
      <ProjectAddMemberForm :id="projectId"/>

      <hr class="border-surface-200 dark:border-surface-700" />

      <div class="flex flex-col gap-3">
        <h4 class="text-xs font-semibold uppercase tracking-wider text-surface-500">
          Учасники проєкту ({{ props.members ? props.members.length + 1 : 0 }})
        </h4>

        <div class="flex flex-col divide-y divide-[var(--p-surface-100)] dark:divide-surface-800">
          <MemberItem
            v-if="props.owner"
            :name="props.owner.name"
            :email="props.owner.email"
            :role="PROJECT_OWNER_ROLE"
            :show-delete-action="false"
          />

          <MemberItem
            v-for="member in props.members"
            :key="member.id"
            :name="member.name"
            :email="member.email"
            :role="member.role"
            @on-delete="selectedMember = member"
          />
        </div>

        <ProjectDeleteMemberModal
          v-model:visible="isOpenDeleteMemeberModal"
          :project-id="projectId"
          :member-id="selectedMember?.id ?? 0"
          :name="selectedMember?.name ?? ''"
          @on-cancel="selectedMember = null"
          @on-done="selectedMember = null"
        />
      </div>
    </div>
  </Dialog>
</template>