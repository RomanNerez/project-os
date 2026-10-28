export const financeRoutes = {
    index: (projectId: number) => `/projects/${projectId}/finances`,
    store: (projectId: number) => `/projects/${projectId}/finances`,
    update: (projectId: number, id: number) => `/projects/${projectId}/finances/${id}`,
};
