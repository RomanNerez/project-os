export const PROJECT_STATUS = {
    DRAFT: 'draft',
    IN_PROGRESS: 'in_progress',
    ON_HOLD: 'on_hold',
    COMPLETED: 'completed',
    CANCELLED: 'cancelled',
} as const

export type ProjectStatus = typeof PROJECT_STATUS[keyof typeof PROJECT_STATUS];

export type ProjectID = number;

export interface Project {
    id: ProjectID;
    title: string;
    description: string;
    status: ProjectStatus;
    budget: number;
}

export type ProjectDraft = Omit<Project, 'id'>;

export const STATUS_META: Record<ProjectStatus, { label: string; severity: 'secondary' | 'warn' | 'danger' }> = {
    draft: { label: 'Чернетка', severity: 'secondary' },
    in_progress: { label: 'У процесі', severity: 'warn' },
    on_hold: { label: 'На паузі', severity: 'danger' },
    completed: { label: 'Завершено', severity: 'danger' },
    cancelled: { label: 'Скасовано', severity: 'danger' },
};

export const STATUS_OPTIONS = (Object.keys(STATUS_META) as ProjectStatus[]).map((value) => ({
    value,
    label: STATUS_META[value].label,
}));

export const emptyProjectDraft = (): ProjectDraft => ({
    title: '',
    description: '',
    status: PROJECT_STATUS.DRAFT,
    budget: 0,
});

export const toDraft = (project: Project): ProjectDraft => ({
    title: project.title,
    description: project.description,
    status: project.status,
    budget: project.budget,
});
