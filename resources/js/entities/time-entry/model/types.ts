import type { IncludedData } from "@/shared/types";

export type TimeEntryID = number;

/** What a new timer is tracked against: one of the user's tasks or a free-form description. */
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
    description: string;
    started_at: string;
    /** `null` while the timer is still running. */
    stopped_at: string | null;
    /** Whole seconds between start and stop, `null` while running. */
    duration: number | null;
    project: TimeEntryProject | null;
    /** The task being tracked, `null` for a free-form description. */
    task: TimeEntryTask | null;
}

export interface TimeEntryIncludes<TUser> extends TimeEntry {
    /** Who tracked the time. */
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
