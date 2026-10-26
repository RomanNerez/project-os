<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ChevronDown from '@primeicons/vue/chevron-down';
import Folder from '@primeicons/vue/folder';
import ListCheck from '@primeicons/vue/list-check';
import Plus from '@primeicons/vue/plus';
import SidebarIcon from '@primeicons/vue/sidebar';
import Stopwatch from '@primeicons/vue/stopwatch';
import Wallet from '@primeicons/vue/wallet';
import type { BreadcrumbItem } from '../model/types';
import { projectRoutes } from '@/entities/project';
import { UserMenu } from '@/entities/user';
import { AppHeader, AppLogo, PageHeader } from '@/shared/ui';
import AiAgentChat from './AiAgentChat.vue';

defineProps<{
    title?: string;
    description?: string;
    breadcrumbs?: BreadcrumbItem[];
}>();

const page = usePage();

const isMobile = ref(false);
const open = ref(true);
let mql = null;
let onMqlChange = null;
onMounted(() => {
    if (typeof window === 'undefined') return;
    mql = window.matchMedia('(max-width: 1023px)');
    isMobile.value = mql.matches;
    open.value = !isMobile.value;
    onMqlChange = (event) => {
        isMobile.value = event.matches;
        open.value = !event.matches;
    };
    mql.addEventListener('change', onMqlChange);
});
onBeforeUnmount(() => {
    if (mql && onMqlChange) {
        mql.removeEventListener('change', onMqlChange);
    }
});
interface NavItem {
    label: string;
    icon: any;
    href: string;
    badge: boolean;
    isActive: boolean;
    subItems?: any[];
}

interface NavGroup {
    label: string;
    action: boolean;
    items: NavItem[];
}

const navGroups = computed<NavGroup[]>(() => [
    {
        label: '',
        action: false,
        items: [
            { icon: Folder, label: 'Проєкти', href: '/projects', badge: false, isActive: page.url.startsWith('/projects') },
            { icon: ListCheck, label: 'Задачі', href: '/tasks', badge: false, isActive: page.url.startsWith('/tasks') },
            { icon: Stopwatch, label: 'Трекер часу', href: '/time-tracker', badge: false, isActive: page.url.startsWith('/time-tracker') },
            { icon: Wallet, label: 'Фінанси', href: '/finances', badge: false, isActive: page.url.startsWith('/finances') },
        ]
    },
]);
</script>

<template>
    <SidebarLayout class="!h-dvh !relative overflow-hidden">
        <SidebarBackdrop v-if="isMobile && open" class="!absolute" />
        <!-- <AiAgentChat /> -->
        <Sidebar id="preview" :collapsible="isMobile ? 'offcanvas' : 'icon'" :overlay="isMobile" v-model:open="open">
            <SidebarSpacer />
            <SidebarAside>
                <SidebarPanel>
                    <SidebarHeader>
                        <Link
                            :href="projectRoutes.index()"
                            class="flex h-16 items-center gap-3 rounded-md px-2 hover:bg-surface-100 dark:hover:bg-surface-800"
                            title="До робочого простору"
                        >
                            <AppLogo />
                            <div>
                                <p class="text-sm font-semibold">Project OS</p>
                                <p class="text-xs text-muted">Робочий простір UNIC</p>
                            </div>
                        </Link>
                    </SidebarHeader>
                    <SidebarContent>
                        <SidebarGroup v-for="group in navGroups" :key="group.label">
                            <SidebarGroupLabel v-if="group.label">{{ group.label }}</SidebarGroupLabel>
                            <SidebarGroupAction v-if="group.action">
                                <Plus />
                            </SidebarGroupAction>
                            <SidebarGroupContent>
                                <SidebarMenu>
                                    <SidebarMenuItem v-for="item in group.items" :key="item.label" :collapsible="!!item.subItems" :defaultOpen="item.subItems ? item.subItems.some((s) => s.isActive) : undefined">
                                        <SidebarMenuButton :as="Link" :href="item.href" :isActive="item.isActive">
                                            <component :is="item.icon" />
                                            <span>{{ item.label }}</span>
                                            <ChevronDown v-if="item.subItems" class="ml-auto transition-transform duration-200 [[data-open]>&]:rotate-180" />
                                        </SidebarMenuButton>
                                        <SidebarMenuBadge v-if="item.badge">{{ item.badge }}</SidebarMenuBadge>
                                        <SidebarMenuSub v-if="item.subItems">
                                            <SidebarMenuSubItem v-for="sub in item.subItems" :key="sub.label">
                                                <SidebarMenuSubButton :isActive="sub.isActive">
                                                    <span>{{ sub.label }}</span>
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        </SidebarMenuSub>
                                    </SidebarMenuItem>
                                </SidebarMenu>
                            </SidebarGroupContent>
                        </SidebarGroup>
                    </SidebarContent>
                    <SidebarRail />
                </SidebarPanel>
            </SidebarAside>
        </Sidebar>
        <SidebarMain class="min-h-0 h-full flex flex-col overflow-hidden">
            <AppHeader>
                <template #start>
                    <SidebarTrigger severity="secondary" :text="true" size="small">
                        <SidebarIcon />
                    </SidebarTrigger>
                    <Button
                        :as="Link"
                        :href="projectRoutes.index()"
                        label="Робочий простір"
                        icon="pi pi-arrow-left"
                        severity="secondary"
                        size="small"
                        text
                    />
                </template>
                <UserMenu />
            </AppHeader>
            <div class="min-h-0 flex-1 p-4 flex flex-col gap-4">
                <PageHeader :title="title" :description="description" :breadcrumbs="breadcrumbs">
                    <template v-if="$slots.actions" #actions>
                        <slot name="actions"></slot>
                    </template>
                </PageHeader>
                <div class="min-h-0 flex-1">
                    <slot></slot>
                </div>
            </div>
        </SidebarMain>
    </SidebarLayout>
</template>
