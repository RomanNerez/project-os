export type NotificationID = string;

/** ListNotificationsController. `title`, `message` and `url` come from the notification's `toArray()`. */
export interface AppNotification {
    id: NotificationID;
    type: string;
    title: string;
    message: string;
    url: string | null;
    read_at: string | null;
    created_at: string;
}

export interface NotificationsResponse {
    data: AppNotification[];
    meta: {
        current_page: number;
        last_page: number;
        unread_count: number;
    };
}
