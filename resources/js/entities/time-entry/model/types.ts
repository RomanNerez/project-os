import type { IncludedData } from "@/shared/types";

export type TimeEntryID = number;

export const TIME_TRACKER_MODE = {
    TASK: 'task',
    FREE: 'free',
} as const;

export type TimeTrackerMode = typeof TIME_TRACKER_MODE[keyof typeof TIME_TRACKER_MODE];

export const TIME_TRACKER_MODE_OPTIONS: { value: TimeTrackerMode; label: string }[] = [
    { value: TIME_TRACKER_MODE.TASK, label: 'Задача' },
    { value: TIME_TRACKER_MODE.FREE, label: 'Довільна назва' },
];

export interface TimeEntryProject {
    id: number;
    title: string;
}

export interface TimeEntryTask {
    id: number;
    title: string;
}

export interface TimeEntry {
    id: TimeEntryID;
    user_id: number;
    description: string;
    started_at: string;
    stopped_at: string | null;
    duration: number | null;
    project: TimeEntryProject | null;
    task: TimeEntryTask | null;
}

export interface TimeEntryIncludes<TUser> extends TimeEntry {
    user: IncludedData<TUser>;
}

export interface TimeEntryDraft {
    description: string;
    task_id: number | null;
}

export const emptyTimeEntryDraft = (): TimeEntryDraft => ({
    description: '',
    task_id: null,
});
