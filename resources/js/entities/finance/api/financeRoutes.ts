export const financeRoutes = {
    index: (projectId: number) => `/projects/${projectId}/finances`,
    store: () => '/finances',
    update: (id: number) => `/finances/${id}`,
};
