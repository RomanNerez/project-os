import type { NotificationID } from '../model/types';

export const notificationRoutes = {
    list: () => '/notifications',
    read: (id: NotificationID) => `/notifications/${id}/read`,
    readAll: () => '/notifications/read-all',
};
