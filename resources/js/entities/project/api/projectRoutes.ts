import type { ProjectID } from '../model/types';

export const projectRoutes = {
    index: () => '/projects',
    store: () => '/projects',
    update: (id: ProjectID) => `/projects/${id}`,
    destroy: (id: ProjectID) => `/projects/${id}`,
};