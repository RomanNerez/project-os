import type { TaskID } from '../model/types';

export const taskRoutes = {
    projectIndex: (projectId: number) => `/projects/${projectId}/tasks`,
    options: (projectId: number) => `/projects/${projectId}/tasks/options`,
    store: (projectId: number) => `/projects/${projectId}/tasks`,
    update: (projectId: number, id: TaskID) => `/projects/${projectId}/tasks/${id}`,
    destroy: (projectId: number, id: TaskID) => `/projects/${projectId}/tasks/${id}`,
    uploadFiles: (projectId: number, id: TaskID) => `/projects/${projectId}/tasks/${id}/files`,
    createTaskComment: (projectId: number, id: TaskID) => `/projects/${projectId}/tasks/${id}/comments`,
};
