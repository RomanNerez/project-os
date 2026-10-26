import { projectRoutes } from '@/entities/project';

export type WorkspaceTabKey = 'overview' | 'projects' | 'calendar' | 'team' | 'analytics' | 'settings';

export interface WorkspaceTab {
    key: WorkspaceTabKey;
    label: string;
    icon: string;
    href?: string;
}

export const WORKSPACE_TABS: WorkspaceTab[] = [
    { key: 'overview', label: 'Огляд', icon: 'pi pi-home' },
    { key: 'projects', label: 'Проєкти', icon: 'pi pi-folder', href: projectRoutes.index() },
    { key: 'calendar', label: 'Календар', icon: 'pi pi-calendar' },
    { key: 'team', label: 'Команда', icon: 'pi pi-users' },
    { key: 'analytics', label: 'Аналітика', icon: 'pi pi-chart-bar' },
    { key: 'settings', label: 'Налаштування', icon: 'pi pi-cog' },
];
