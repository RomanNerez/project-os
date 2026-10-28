import type { TimeEntryID } from '../model/types';

export const timeEntryRoutes = {
    index: (projectId: number) => `/projects/${projectId}/time-tracker`,
    start: (projectId: number) => `/projects/${projectId}/time-tracker/start`,
    stop: (projectId: number) => `/projects/${projectId}/time-tracker/stop`,
    destroy: (projectId: number, id: TimeEntryID) => `/projects/${projectId}/time-tracker/entries/${id}`,
};
