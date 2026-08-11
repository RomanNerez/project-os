<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import ChevronDown from '@primeicons/vue/chevron-down';
import Folder from '@primeicons/vue/folder';
import Plus from '@primeicons/vue/plus';
import SidebarIcon from '@primeicons/vue/sidebar';
import type { BreadcrumbItem } from '../model/types';

defineProps<{
    title?: string;
    description?: string;
    breadcrumbs?: BreadcrumbItem[];
}>();

const page = usePage();
const user = computed(() => page.props.auth?.user);
const userInitials = computed(() => {
    const name = user.value?.name ?? '';
    return name
        .split(' ')
        .filter(Boolean)
        .map((part: string) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase() || '?';
});

const userMenu = ref();
const userMenuItems = [
    {
        label: 'Вийти',
        icon: 'pi pi-sign-out',
        command: () => router.post('/logout'),
    },
];

function toggleUserMenu(event: Event): void {
    userMenu.value?.toggle(event);
}

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
const navGroups: {
    label: string;
    action: boolean;
    items: {
        label: string;
        icon: any;
        badge: boolean;
        isActive?: boolean;
        subItems?: any[]
    }[]
}[] = [
    {
        label: '',
        action: false,
        items: [
            { icon: Folder, label: 'Проєкти', badge: false }
        ]
    },
];
</script>

<template>
    <SidebarLayout class="h-dvh! relative! overflow-hidden">
        <SidebarBackdrop v-if="isMobile && open" class="absolute!" />
        <Sidebar id="preview" :collapsible="isMobile ? 'offcanvas' : 'icon'" :overlay="isMobile" v-model:open="open">
            <SidebarSpacer />
            <SidebarAside>
                <SidebarPanel>
                    <SidebarHeader>
                        <div class="flex h-16 items-center gap-3 px-2">
                            <Avatar size="large">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles" aria-hidden="true">
                                    <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path>
                                    <path d="M20 2v4"></path>
                                    <path d="M22 4h-4"></path>
                                    <circle cx="4" cy="20" r="2"></circle>
                                </svg>
                            </Avatar>
                            <div>
                                <p class="text-sm font-semibold">Project OS</p>
                                <p class="text-xs text-muted">Робочий простір UNIC</p>
                            </div>
                        </div>
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
                                        <SidebarMenuButton :isActive="item.isActive">
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
            <header class="flex h-12 shrink-0 items-center gap-2 border-b border-surface-200 dark:border-surface-700 px-4">
                <SidebarTrigger severity="secondary" :text="true" size="small">
                    <SidebarIcon />
                </SidebarTrigger>
                <button
                    type="button"
                    class="ml-auto flex cursor-pointer items-center gap-2 rounded-md px-2 py-1 hover:bg-surface-100 dark:hover:bg-surface-800"
                    @click="toggleUserMenu"
                >
                    <Avatar :label="userInitials" shape="circle" class="size-6 shrink-0 text-xs" />
                    <span class="text-sm">{{ user?.name ?? 'Користувач' }}</span>
                    <ChevronDown class="size-3 text-muted" />
                </button>
                <Menu ref="userMenu" :model="userMenuItems" :popup="true" />
            </header>
            <div class="min-h-0 flex-1 overflow-y-auto p-4 flex flex-col gap-4">
                <div class="rounded-lg bg-surface-100 dark:bg-surface-800 flex-1 min-h-0">
                    <div
                        v-if="title || description || breadcrumbs?.length || $slots.actions"
                        class="shrink-0 px-4 py-4 dark:border-surface-700 dark:bg-surface-900"
                    >
                        <Breadcrumb v-if="breadcrumbs?.length" :model="breadcrumbs" />
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h1 v-if="title" class="text-2xl font-semibold">{{ title }}</h1>
                                <p v-if="description" class="mt-1 text-sm text-muted-color">{{ description }}</p>
                            </div>
                            <div v-if="$slots.actions" class="flex items-center gap-2">
                                <slot name="actions"></slot>
                            </div>
                        </div>
                    </div>
                    <slot></slot>
                </div>
            </div>
        </SidebarMain>
    </SidebarLayout>
</template>
