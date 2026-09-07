<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout } from '@/widgets/admin-layout';
import { ProjectCard, type Project, type ProjectID, type ProjectIncludes, type ProjectMemeber } from '@/entities/project';
import { ProjectFormModal } from '@/features/project-form';
import { ProjectDeleteModal } from '@/features/project-delete';
import { EmptyList } from '@/shared/ui';
import type { PaginatedServerData } from '@/shared/types';
import type { User } from '@/entities/user';
import { ProjectMembersModal } from '@/widgets/project-members';

type ProjectItem = ProjectIncludes<User, ProjectMemeber[]>;

interface Props {
  projects: PaginatedServerData<ProjectItem[]>
}

const props = defineProps<Props>();

const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isShowMembersModal = ref(false);
const selectedProjectId = ref<ProjectID | null>(null);
const selectedProject = computed<ProjectItem | null>(
    () => props.projects.data.find(p => p.id === selectedProjectId.value) ?? null
)

function openCreate(): void {
    selectedProjectId.value = null;
    isEditModalOpen.value = true;
}

function openEdit(projectId: ProjectID): void {
    selectedProjectId.value = projectId;
    isEditModalOpen.value = true;
}

function openDelete(projectId: ProjectID): void {
    selectedProjectId.value = projectId;
    isDeleteModalOpen.value = true;
}

function openManagerMembers(projectId: ProjectID): void {
    selectedProjectId.value = projectId;
    isShowMembersModal.value = true;
}

</script>

<template>
    <Head title="Проєкти" />

    <AdminLayout
        title="Проєкти"
        description="Керуйте проєктами робочого простору"
    >
        <template #actions>
            <Button label="Новий проєкт" icon="pi pi-plus" @click="openCreate" />
        </template>

        <div class="flex h-full flex-col gap-4 overflow-y-auto">
            <div v-if="props.projects.data.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <ProjectCard
                    v-for="project in props.projects.data"
                    :key="project.id"
                    :id="project.id"
                    :title="project.title"
                    :description="project.description"
                    :active-until="project.active_until"
                    :proejct-status="project.status"
                    :budget="project.budget"
                    :user-name="project.user.data.name"
                    :members="project.members.data"
                    @edit="openEdit(project.id)"
                    @delete="openDelete(project.id)"
                    @manage-members="openManagerMembers(project.id)"
                />
            </div>

            <EmptyList
                v-else
                icon-class="pi-folder-open"
                decription="Проєктів поки немає"
                button-label="Створити перший проєкт"
                @on-handler="openCreate"
            />

            <ProjectFormModal
                v-model:visible="isEditModalOpen"
                :project="selectedProject"
                @done="selectedProject = null"
                @cancel="selectedProject = null"
            />

            <ProjectDeleteModal
                v-model:visible="isDeleteModalOpen"
                :project="selectedProject"
                @done="selectedProject = null"
                @cancel="selectedProject = null"
            />

            <ProjectMembersModal
                v-model:visible="isShowMembersModal"
                :project-id="selectedProject?.id ?? 0"
                :owner="selectedProject?.user.data"
                :members="selectedProject?.members.data"
            />
        </div>
    </AdminLayout>
</template>
