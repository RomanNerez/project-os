import type { TaskID } from '../model/types';

export const taskRoutes = {
    index: () => '/tasks',
    store: (projectId: number) => `/projects/${projectId}/tasks`,
    update: (projectId: number, id: TaskID) => `/projects/${projectId}/tasks/${id}`,
    destroy: (id: TaskID) => `/tasks/${id}`,
    uploadFiles: (id: TaskID) => `/tasks/${id}/files`,
    createTaskComment: (id: TaskID) => `/tasks/${id}/comment`,
};
