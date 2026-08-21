import type { TaskID } from '../model/types';

export const taskRoutes = {
    index: () => '/tasks',
    store: () => '/tasks',
    update: (id: TaskID) => `/tasks/${id}`,
    updateStatus: (id: TaskID) => `/tasks/${id}/status`,
    destroy: (id: TaskID) => `/tasks/${id}`,
};
