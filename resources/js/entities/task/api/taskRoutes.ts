import type { TaskID } from '../model/types';

export const taskRoutes = {
    projectIndex: (projectId: number) => `/projects/${projectId}/tasks`,
    options: (projectId: number) => `/projects/${projectId}/tasks/options`,
    store: (projectId: number) => `/projects/${projectId}/tasks`,
    update: (projectId: number, id: TaskID) => `/projects/${projectId}/tasks/${id}`,
    destroy: (projectId: number, id: TaskID) => `/projects/${projectId}/tasks/${id}`,
    uploadFiles: (id: TaskID) => `/tasks/${id}/files`,
    createTaskComment: (id: TaskID) => `/tasks/${id}/comment`,
};
