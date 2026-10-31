import { eventRoutes } from '@/entities/event';
import { projectRoutes } from '@/entities/project';
import { roleRoutes } from '@/entities/role';
import { userRoutes, type User } from '@/entities/user';

export type WorkspaceTabKey = 'overview' | 'projects' | 'calendar' | 'team' | 'analytics' | 'settings';

export type WorkspacePermission = keyof NonNullable<User['permissions']>;

export interface WorkspaceTab {
    key: WorkspaceTabKey;
    label: string;
    icon: string;
    href?: string;
    /** Without it the tab stays disabled. */
    permission?: WorkspacePermission;
}

export const WORKSPACE_TABS: WorkspaceTab[] = [
    // { key: 'overview', label: 'Огляд', icon: 'pi pi-home' },
    { key: 'projects', label: 'Проєкти', icon: 'pi pi-folder', href: projectRoutes.index() },
    { key: 'calendar', label: 'Календар', icon: 'pi pi-calendar', href: eventRoutes.index() },
    { key: 'team', label: 'Команда', icon: 'pi pi-users', href: userRoutes.index(), permission: 'manage_users' },
    // { key: 'analytics', label: 'Аналітика', icon: 'pi pi-chart-bar' },
    { key: 'settings', label: 'Налаштування', icon: 'pi pi-cog', href: roleRoutes.index(), permission: 'manage_roles' },
];
