<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import { ProjectAddMemberForm } from '@/features/project-add-member';
import { MemberItem, PROJECT_OWNER_ROLE } from '@/entities/project';
import type { ProjectMemeber } from '@/entities/project';

interface Owner {
  id: number;
  name: string;
  email: string;
}

interface Props {
  visible: boolean;
  projectId: number;
  owner?: Owner;
  members?: ProjectMemeber[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:visible', value: boolean): void;
}>();

const deleteForm = useForm({});

const removeMember = (userId: number) => {
  deleteForm.delete(route('projects.members.destroy', [props.projectId, userId]), {
    preserveScroll: true,
  });
};

const closeModal = () => {
  emit('update:visible', false);
};
</script>

<template>
  <Dialog
    :visible="props.visible"
    modal
    header="Управління командою проєкту"
    :style="{ width: '32rem' }"
    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    @update:visible="closeModal"
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
            @on-delete="removeMember(member.id)"
          />
        </div>
      </div>
    </div>
  </Dialog>
</template>