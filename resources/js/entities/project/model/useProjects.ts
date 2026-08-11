import { ref } from 'vue';
import type { Project, ProjectDraft } from './types';

const projects = ref<Project[]>([
    {
        id: 1,
        name: 'Project OS',
        description: 'Внутрішня платформа для управління проєктами робочого простору UNIC.',
        activeUntil: new Date('2026-12-31'),
        priority: 'high',
        progress: 65,
        budget: 24000,
    },
    {
        id: 2,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
    {
        id: 3,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
    {
        id: 4,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
    {
        id: 5,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
    {
        id: 6,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
    {
        id: 7,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
    {
        id: 8,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
    {
        id: 9,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
    {
        id: 10,
        name: 'Landing UNIC',
        description: 'Редизайн головного лендінгу та оновлення брендингу.',
        activeUntil: new Date('2026-09-15'),
        priority: 'medium',
        progress: 30,
        budget: 8500,
    },
]);

let nextId = 3;

export function useProjects() {
    function createProject(draft: ProjectDraft): void {
        projects.value.push({ id: nextId++, ...draft });
    }

    function updateProject(id: number, draft: ProjectDraft): void {
        const index = projects.value.findIndex((project) => project.id === id);
        if (index !== -1) {
            projects.value[index] = { id, ...draft };
        }
    }

    function removeProject(id: number): void {
        projects.value = projects.value.filter((project) => project.id !== id);
    }

    return { projects, createProject, updateProject, removeProject };
}
