export type TimeEntryID = number;

export interface TimeEntryProject {
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
}

export interface TimeEntryDraft {
    description: string;
    project_id: number | null;
}

export const emptyTimeEntryDraft = (): TimeEntryDraft => ({
    description: '',
    project_id: null,
});
