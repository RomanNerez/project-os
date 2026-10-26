<script setup lang="ts">
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import ChevronDown from '@primeicons/vue/chevron-down';
import { AuthAvatar } from '@/shared/ui';
import { userRoutes } from '../api/userRoutes';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const menu = ref();
const items = [
    {
        label: 'Профіль',
        icon: 'pi pi-user',
        command: () => router.visit(userRoutes.profile()),
    },
    { separator: true },
    {
        label: 'Вийти',
        icon: 'pi pi-sign-out',
        command: () => router.post('/logout'),
    },
];

function toggle(event: Event): void {
    menu.value?.toggle(event);
}
</script>

<template>
    <button
        type="button"
        class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1 hover:bg-surface-100 dark:hover:bg-surface-800"
        @click="toggle"
    >
        <AuthAvatar size="small" />
        <span class="text-sm">{{ user?.name ?? 'Користувач' }}</span>
        <ChevronDown class="size-3 text-muted" />
    </button>
    <Menu ref="menu" :model="items" :popup="true" />
</template>
