<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout } from '@/widgets/admin-layout';
import { ProjectCard, useProjects, type Project, type ProjectDraft } from '@/entities/project';
import { ProjectFormModal } from '@/features/project-form';

const { projects, createProject, updateProject, removeProject } = useProjects();

const isModalOpen = ref(false);
const editingProject = ref<Project | null>(null);

function openCreate(): void {
    editingProject.value = null;
    isModalOpen.value = true;
}

function openEdit(project: Project): void {
    editingProject.value = project;
    isModalOpen.value = true;
}

function handleSubmit(draft: ProjectDraft): void {
    if (editingProject.value) {
        updateProject(editingProject.value.id, draft);
    } else {
        createProject(draft);
    }
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

        <div class="flex h-full flex-col gap-4 p-4">
            <div v-if="projects.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <ProjectCard
                    v-for="project in projects"
                    :key="project.id"
                    :project="project"
                    @edit="openEdit(project)"
                    @delete="removeProject(project.id)"
                />
            </div>

            <div v-else class="flex flex-col items-center gap-3 rounded-lg border border-dashed border-surface-300 py-16 dark:border-surface-600">
                <i class="pi pi-folder-open text-3xl text-muted-color"></i>
                <p class="text-muted-color">Проєктів поки немає</p>
                <Button label="Створити перший проєкт" icon="pi pi-plus" text @click="openCreate" />
            </div>

            <ProjectFormModal v-model:visible="isModalOpen" :project="editingProject" @submit="handleSubmit" />
        </div>
    </AdminLayout>
</template>
