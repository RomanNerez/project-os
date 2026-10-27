import { computed, ref, watch } from 'vue';
import { useHttp, usePage } from '@inertiajs/vue3';
import { notificationRoutes } from '../api/notificationRoutes';
import type { AppNotification, NotificationID, NotificationsResponse } from './types';

/** The current user's notifications: a paged list plus the unread badge count. */
export function useNotifications() {
    const page = usePage();

    const items = ref<AppNotification[]>([]);
    const currentPage = ref(0);
    const lastPage = ref(1);
    const loaded = ref(false);

    // Seeded from the shared prop so the badge shows before the list is fetched.
    const unreadCount = ref(page.props.auth.user?.unread_notifications_count ?? 0);
    watch(() => page.props.auth.user?.unread_notifications_count, (count) => {
        unreadCount.value = count ?? 0;
    });

    const listHttp = useHttp<{ page: number }, NotificationsResponse>({ page: 1 });
    const actionHttp = useHttp<Record<string, never>, { unread_count: number }>({});

    async function fetchPage(pageNumber: number): Promise<void> {
        listHttp.cancel();
        listHttp.page = pageNumber;

        try {
            const response = await listHttp.get(notificationRoutes.list());
            items.value = pageNumber === 1 ? response.data : [...items.value, ...response.data];
            currentPage.value = response.meta.current_page;
            lastPage.value = response.meta.last_page;
            unreadCount.value = response.meta.unread_count;
            loaded.value = true;
        } catch {
            // Cancelled by a newer load or failed: keep the current list.
        }
    }

    function load(): Promise<void> {
        return fetchPage(1);
    }

    function loadMore(): Promise<void> {
        return fetchPage(currentPage.value + 1);
    }

    async function markRead(id: NotificationID): Promise<void> {
        const notification = items.value.find((item) => item.id === id);
        if (!notification || notification.read_at) return;

        try {
            const response = await actionHttp.patch(notificationRoutes.read(id));
            notification.read_at = new Date().toISOString();
            unreadCount.value = response.unread_count;
        } catch {
            // Leave it unread; the next load shows the real state.
        }
    }

    async function markAllRead(): Promise<void> {
        try {
            const response = await actionHttp.patch(notificationRoutes.readAll());
            const now = new Date().toISOString();
            items.value.forEach((item) => { item.read_at ??= now; });
            unreadCount.value = response.unread_count;
        } catch {
            // Leave them unread; the next load shows the real state.
        }
    }

    return {
        items,
        unreadCount,
        loaded,
        hasMore: computed(() => currentPage.value < lastPage.value),
        loading: computed(() => listHttp.processing),
        processing: computed(() => actionHttp.processing),
        load,
        loadMore,
        markRead,
        markAllRead,
    };
}
