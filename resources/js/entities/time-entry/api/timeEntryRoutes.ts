import type { TimeEntryID } from '../model/types';

export const timeEntryRoutes = {
    index: () => '/time-tracker',
    start: () => '/time-tracker/start',
    stop: () => '/time-tracker/stop',
    destroy: (id: TimeEntryID) => `/time-tracker/entries/${id}`,
};
