import type { IncludedData } from "@/shared/types";

export const TASK_STATUS = {
    TODO: 'todo',
    IN_PROGRESS: 'in_progress',
    IN_REVIEW: 'in_review',
    DONE: 'done',
    CANCELLED: 'cancelled',
} as const

export type TaskStatus = typeof TASK_STATUS[keyof typeof TASK_STATUS];

export type TaskID = number;

export interface TaskProject {
    id: number;
    title: string;
}

export interface TaskAssignee {
    id: number;
    name: string;
}

export interface Task {
    id: TaskID;
    title: string;
    description: string | null;
    status: TaskStatus | null;
    project_id: number | null;
    assignee_id: number | null;
}

export interface TaskIncludes<TMedia = null, TProject = null, TAssignee = null, TComments = null> extends Task {
    media: IncludedData<TMedia>;
    project: TProject extends null ? null : IncludedData<TProject>;
    assignee: TAssignee extends null ? null : IncludedData<TAssignee>;
    comments: TComments extends null ? null : IncludedData<TComments>
}

/**
 * The API returns nested project/assignee objects, while the form submits ids,
 * so the draft is not a plain Omit of the entity.
 */
export interface TaskDraft {
    title: string;
    description: string;
    status: TaskStatus | null;
    project_id: number | null;
    assignee_id: number | null;
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

export const emptyTaskDraft = (): TaskDraft => ({
    title: '',
    description: '',
    status: TASK_STATUS.TODO,
    project_id: null,
    assignee_id: null,
});

export const toDraft = (task: Task): TaskDraft => ({
    title: task.title,
    description: task.description ?? '',
    status: task.status,
    project_id: task.project_id ?? null,
    assignee_id: task.assignee_id ?? null,
});
