<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { NotificationItem, useNotifications, type AppNotification } from '@/entities/notification';
import { EmptyList } from '@/shared/ui';

const visible = ref(false);

const {
    items,
    unreadCount,
    loaded,
    hasMore,
    loading,
    processing,
    load,
    loadMore,
    markRead,
    markAllRead,
} = useNotifications();

function open(): void {
    visible.value = true;
    // Refetch on every open so the list never lags behind the badge.
    void load();
}

async function select(notification: AppNotification): Promise<void> {
    await markRead(notification.id);

    if (notification.url) {
        visible.value = false;
        router.visit(notification.url);
    }
}
</script>

<template>
    <OverlayBadge
        v-if="unreadCount > 0"
        :value="unreadCount > 99 ? '99+' : unreadCount"
        severity="danger"
        size="small"
    >
        <Button icon="pi pi-bell" severity="secondary" text rounded aria-label="Нотифікації" @click="open" />
    </OverlayBadge>
    <Button v-else icon="pi pi-bell" severity="secondary" text rounded aria-label="Нотифікації" @click="open" />

    <Drawer v-model:visible="visible" position="right" class="!w-full sm:!w-[26rem]">
        <template #header>
            <div class="flex flex-1 items-center justify-between gap-2 pr-2">
                <span class="text-lg font-semibold">Нотифікації</span>
                <Button
                    label="Позначити всі прочитаними"
                    size="small"
                    text
                    :disabled="unreadCount === 0 || processing"
                    @click="markAllRead"
                />
            </div>
        </template>

        <div v-if="!loaded && loading" class="flex flex-col gap-3">
            <Skeleton v-for="i in 4" :key="i" height="3.5rem" />
        </div>

        <EmptyList
            v-else-if="loaded && items.length === 0"
            icon-class="pi-bell"
            decription="Нотифікацій поки немає"
            :show-action="false"
        />

        <div v-else class="flex flex-col gap-1">
            <NotificationItem
                v-for="notification in items"
                :key="notification.id"
                :notification="notification"
                @select="select"
            />
            <Button
                v-if="hasMore"
                label="Завантажити ще"
                size="small"
                text
                class="mt-2 self-center"
                :loading="loading"
                @click="loadMore"
            />
        </div>
    </Drawer>
</template>
