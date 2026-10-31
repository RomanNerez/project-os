<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { roleRoutes, type RolePermission, type WorkspaceRole } from '@/entities/role';
import type { ServerData } from '@/shared/types';
import { SettingsPermissions } from '@/widgets/settings-permissions';
import { SettingsRoles } from '@/widgets/settings-roles';
import { WorkspaceLayout } from '@/widgets/workspace-layout';

type SettingsTabKey = 'roles' | 'permissions';

/** Each tab is its own route, so a tab only loads its own data. */
const SETTINGS_TABS: { key: SettingsTabKey; label: string; icon: string; href: string }[] = [
    { key: 'roles', label: 'Ролі', icon: 'pi pi-users', href: roleRoutes.index() },
    { key: 'permissions', label: 'Доступи', icon: 'pi pi-shield', href: roleRoutes.permissions() },
];

const props = defineProps<{
    tab: SettingsTabKey;
    roles?: ServerData<WorkspaceRole[]>;
    permissions?: RolePermission[];
}>();

function openTab(key: string | number): void {
    const tab = SETTINGS_TABS.find((item) => item.key === key);

    if (tab && tab.key !== props.tab) {
        router.visit(tab.href);
    }
}
</script>

<template>
    <Head title="Налаштування" />

    <WorkspaceLayout active="settings" description="Налаштування робочого простору">
        <div class="rounded-lg border border-[var(--p-content-border-color)] bg-[var(--p-content-background)] shadow-sm">
            <Tabs :value="tab" @update:value="openTab">
                <TabList>
                    <Tab v-for="item in SETTINGS_TABS" :key="item.key" :value="item.key" class="flex items-center gap-2">
                        <i :class="item.icon" />
                        <span>{{ item.label }}</span>
                    </Tab>
                </TabList>
                <TabPanels>
                    <TabPanel value="roles">
                        <SettingsRoles v-if="tab === 'roles' && roles" :roles="roles.data" />
                    </TabPanel>
                    <TabPanel value="permissions">
                        <SettingsPermissions
                            v-if="tab === 'permissions' && roles && permissions"
                            :roles="roles.data"
                            :permissions="permissions"
                        />
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </WorkspaceLayout>
</template>
