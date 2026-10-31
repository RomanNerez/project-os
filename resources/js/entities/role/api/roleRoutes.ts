import type { RoleID } from '../model/types';

export const roleRoutes = {
    index: () => '/settings/roles',
    permissions: () => '/settings/permissions',
    store: () => '/settings/roles',
    update: (id: RoleID) => `/settings/roles/${id}`,
    destroy: (id: RoleID) => `/settings/roles/${id}`,
    assignToUser: (userId: number) => `/users/${userId}/role`,
};
