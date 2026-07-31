export type ProjectPriority = 'low' | 'medium' | 'high';

export interface Project {
    id: number;
    name: string;
    description: string;
    activeUntil: Date;
    priority: ProjectPriority;
    /** Прогрес виконання, 0–100 */
    progress: number;
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
