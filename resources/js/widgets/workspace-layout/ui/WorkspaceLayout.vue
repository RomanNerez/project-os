<script setup lang="ts">
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { UserMenu } from '@/entities/user';
import { NotificationsBell } from '@/features/notifications-panel';
import { AppHeader, AppLogo, PageHeader } from '@/shared/ui';
import { WORKSPACE_TABS, type WorkspaceTabKey } from '../model/tabs';

withDefaults(defineProps<{
    active?: WorkspaceTabKey | '';
    title?: string;
    description?: string;
}>(), {
    active: '',
    title: 'Робочий простір UNIC',
});

const page = usePage();

const tabs = computed(() => WORKSPACE_TABS.filter((tab) => {
    return !tab.permission || !!page.props.auth.user?.permissions?.[tab.permission];
}).map((tab) => ({ ...tab, href: tab.href })));
</script>

<template>
    <div class="flex h-dvh flex-col overflow-hidden bg-surface-50 dark:bg-surface-900 gap-4">
        <AppHeader>
            <template #start>
                <AppLogo size="normal" />
                <span class="text-sm font-semibold">Project OS</span>
            </template>
            <NotificationsBell />
            <UserMenu />
        </AppHeader>

        <nav class="shrink-0">
            <Tabs :value="active" scrollable class="mx-auto w-full max-w-7xl px-4"
                :pt="{
                    root: {
                        class: 'shrink-0 rounded-lg !border border-[var(--p-content-border-color)] bg-[var(--p-content-background)] shadow-sm'
                    },
                }"
            >
                <TabList :pt="{
                    root: { class: '!border-0 ' },
                    tabList: { class: '!bg-transparent !border-0 ' }
                }">
                    <Tab
                        v-for="tab in tabs"
                        :key="tab.key"
                        :value="tab.key"
                        :disabled="!tab.href"
                        class="flex items-center gap-2"
                        @click="tab.href && tab.key !== active && router.visit(tab.href)"
                    >
                        <i :class="tab.icon" />
                        <span>{{ tab.label }}</span>
                    </Tab>
                </TabList>
            </Tabs>
        </nav>

        <div class="mx-auto flex min-h-0 w-full max-w-7xl flex-1 flex-col gap-4">
            <PageHeader :title="title" :description="description">
                <template v-if="$slots.actions" #actions>
                    <slot name="actions"></slot>
                </template>
            </PageHeader>

            <div class="min-h-0 flex-1">
                <slot></slot>
            </div>
        </div>
    </div>
</template>
