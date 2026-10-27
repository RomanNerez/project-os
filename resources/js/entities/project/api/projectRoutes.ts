import type { ProjectID } from '../model/types';

export const projectRoutes = {
    index: () => '/projects',
    show: (id: ProjectID) => `/projects/${id}`,
    members: (id: ProjectID) => `/projects/${id}/members`,
    store: () => '/projects',
    update: (id: ProjectID) => `/projects/${id}`,
    destroy: (id: ProjectID) => `/projects/${id}`,
};