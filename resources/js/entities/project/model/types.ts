import type { IncludedData } from "@/shared/types";

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
    active_until: string | null;
}

export interface ProjectMemeber {
    id: number;
    name: string;
    email: string;
    role: typeof MEMEBER_ROLE[keyof typeof MEMEBER_ROLE]
}

export interface ProjectIncludes<TUser = null, TMembers = null> extends Project {
    user: IncludedData<TUser>;
    members: TMembers extends null ? null : IncludedData<TMembers>;
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
    active_until: null,
});

export const toDraft = (project: Project): ProjectDraft => ({
    title: project.title,
    description: project.description,
    status: project.status,
    budget: project.budget,
    active_until: project.active_until,
});

export const PROJECT_OWNER_ROLE = 'owner';

export const MEMEBER_ROLE = {
    ADMIN: 'admin',
    MEMBER: 'member',
    VIEWER: 'viewer',
} as const;

export const getRoleLabel = (role?: string) => {
    switch (role) {
        case MEMEBER_ROLE.ADMIN: return 'Адмін';
        case MEMEBER_ROLE.VIEWER: return 'Спостерігач';
        case PROJECT_OWNER_ROLE: return 'Власник';
        default: return 'Учасник';
    }
};

export const getRoleSeverity = (role?: string) => {
    switch (role) {
      case MEMEBER_ROLE.ADMIN: return 'warn';
      case MEMEBER_ROLE.VIEWER: return 'info';
      case PROJECT_OWNER_ROLE: return 'contrast';
      default: return 'secondary';
    }
};
