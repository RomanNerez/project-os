import type { IncludedData } from "@/shared/types";

export const TASK_STATUS = {
    TODO: 'todo',
    IN_PROGRESS: 'in_progress',
    IN_REVIEW: 'in_review',
    DONE: 'done',
    CANCELLED: 'cancelled',
} as const

export type TaskStatus = typeof TASK_STATUS[keyof typeof TASK_STATUS];

export const TASK_PRIORITY = {
    LOW: 'low',
    MEDIUM: 'medium',
    HIGH: 'high',
    URGENT: 'urgent',
} as const

export type TaskPriority = typeof TASK_PRIORITY[keyof typeof TASK_PRIORITY];

export type TaskID = number;

export interface TaskProject {
    id: number;
    title: string;
}

export interface TaskAssignee {
    id: number;
    name: string;
}

export interface TaskPermissions {
    comment: boolean;
    upload_files: boolean;
    delete_files: boolean;
}

export interface Task {
    id: TaskID;
    title: string;
    description: string | null;
    status: TaskStatus | null;
    priority: TaskPriority;
    /** Total estimate for everyone working on the task, in minutes. */
    estimate: number | null;
    project_id: number;
    assignee_id: number | null;
    permissions: TaskPermissions;
}

export type TaskEditableField = 'title' | 'description' | 'status' | 'priority' | 'estimate' | 'assignee_id';

export type TaskWithAssignee = Task & {
    assignee?: IncludedData<TaskAssignee> | null;
};

export interface TaskIncludes<TMedia = null, TProject = null, TAssignee = null, TComments = null> extends Task {
    media: IncludedData<TMedia>;
    project: TProject extends null ? null : IncludedData<TProject>;
    assignee: TAssignee extends null ? null : IncludedData<TAssignee>;
    comments: TComments extends null ? null : IncludedData<TComments>
}

/** Creating a task only needs a title and the project it belongs to. */
export interface TaskCreateDraft {
    title: string;
    project_id: number | null;
    status: TaskStatus | null;
}

type Severity = 'secondary' | 'info' | 'warn' | 'success' | 'danger';

export const STATUS_META: Record<TaskStatus, { label: string; severity: Severity }> = {
    todo: { label: 'До виконання', severity: 'secondary' },
    in_progress: { label: 'У процесі', severity: 'info' },
    in_review: { label: 'На перевірці', severity: 'warn' },
    done: { label: 'Завершено', severity: 'success' },
    cancelled: { label: 'Скасовано', severity: 'danger' },
};

export const STATUS_OPTIONS = (Object.keys(STATUS_META) as TaskStatus[]).map((value) => ({
    value,
    label: STATUS_META[value].label,
}));

export const PRIORITY_META: Record<TaskPriority, { label: string; severity: Severity; icon: string }> = {
    low: { label: 'Низький', severity: 'secondary', icon: 'pi pi-angle-down' },
    medium: { label: 'Середній', severity: 'info', icon: 'pi pi-equals' },
    high: { label: 'Високий', severity: 'warn', icon: 'pi pi-angle-up' },
    urgent: { label: 'Терміновий', severity: 'danger', icon: 'pi pi-angle-double-up' },
};

export const PRIORITY_OPTIONS = (Object.keys(PRIORITY_META) as TaskPriority[]).map((value) => ({
    value,
    label: PRIORITY_META[value].label,
}));

export const emptyTaskCreateDraft = (): TaskCreateDraft => ({
    title: '',
    project_id: null,
    status: TASK_STATUS.TODO,
});

/** A task as offered by `TaskSelect`: listed from `projects.tasks.options` as JSON, with its project. */
export type TaskOption = Task & {
    project: IncludedData<TaskProject> | null;
};

/** What `TaskMultiSelect` needs of a selected task, so callers can pre-select tasks they only know by id and title. */
export type TaskSelectOption = Pick<Task, 'id' | 'title'> & {
    project: IncludedData<TaskProject> | null;
};
