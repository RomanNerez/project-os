export type ProjectPriority = 'low' | 'medium' | 'high';

export type ProjectID = number;

export interface Project {
    id: ProjectID;
    title: string;
    description: string;
    status: string;
    budget: number;
}

export type ProjectDraft = Omit<Project, 'id'>;

export const PRIORITY_META: Record<ProjectPriority, { label: string; severity: 'secondary' | 'warn' | 'danger' }> = {
    low: { label: 'Низький', severity: 'secondary' },
    medium: { label: 'Середній', severity: 'warn' },
    high: { label: 'Високий', severity: 'danger' },
};

export const PRIORITY_OPTIONS = (Object.keys(PRIORITY_META) as ProjectPriority[]).map((value) => ({
    value,
    label: PRIORITY_META[value].label,
}));

export const emptyProjectDraft = (): ProjectDraft => ({
    title: '',
    description: '',
    status: '',
    budget: 0,
});

export const toDraft = (project: Project): ProjectDraft => ({
    title: project.title,
    description: project.description,
    status: project.status,
    budget: project.budget,
});
