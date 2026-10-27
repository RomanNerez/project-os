<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { ProjectLayout, type BreadcrumbItem } from '@/widgets/project-layout';
import { ProjectMembersTable } from '@/widgets/project-members';
import { ProjectAddMemberModal } from '@/features/project-add-member';
import { projectRoutes, type ProjectIncludes, type ProjectMember } from '@/entities/project';

const props = defineProps<{
    project: {
        data: ProjectIncludes<null, ProjectMember[]>;
    };
}>();

const project = computed(() => props.project.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { label: 'Проєкти', href: projectRoutes.index() },
    { label: project.value.title, href: projectRoutes.show(project.value.id) },
    { label: 'Учасники' },
]);

const isAddModalOpen = ref(false);
</script>

<template>
    <Head title="Учасники" />

    <ProjectLayout
        title="Учасники"
        :description="`Команда проєкту «${project.title}»`"
        :breadcrumbs="breadcrumbs"
    >
        <template v-if="project.permissions.manage_members" #actions>
            <Button label="Запросити учасника" icon="pi pi-user-plus" @click="isAddModalOpen = true" />
        </template>

        <div class="h-full overflow-y-auto">
            <ProjectMembersTable
                :project-id="project.id"
                :members="project.members.data"
                :can-manage="project.permissions.manage_members"
            />
        </div>

        <ProjectAddMemberModal v-model:visible="isAddModalOpen" :project-id="project.id" />
    </ProjectLayout>
</template>
