import { computed, ref, type ComputedRef, type Ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import { buildSearchParam, debounce, sanitizeSearchText, SEARCH_JOIN } from '@/shared/lib';
import { projectRoutes } from '../api/projectRoutes';
import type { MemberRole, Project } from './types';

interface ProjectOptionsQuery {
    search: string;
    searchJoin: typeof SEARCH_JOIN.AND;
    limit: number;
    exclude_roles: MemberRole[];
}

interface UseProjectOptions {
    projects: Ref<Project[]>;
    loading: ComputedRef<boolean>;
    load: (title?: string) => Promise<void>;
    search: (title: string) => void;
}

/**
 * Projects available to the current user, loaded from `projects.index` as JSON.
 * `excludeRoles` hides projects where the user is a member with one of these roles.
 */
export function useProjectOptions(excludeRoles: () => MemberRole[] = () => []): UseProjectOptions {
    const projects = ref<Project[]>([]);

    const http = useHttp<ProjectOptionsQuery, { data: Project[] }>({
        search: '',
        searchJoin: SEARCH_JOIN.AND,
        limit: 50,
        exclude_roles: [],
    });

    async function load(title = ''): Promise<void> {
        const value = sanitizeSearchText(title);

        http.cancel();
        http.search = value ? buildSearchParam({ title: value }) : '';
        http.exclude_roles = excludeRoles();

        try {
            const response = await http.get(projectRoutes.index());
            projects.value = response?.data ?? [];
        } catch {
            // Cancelled by a newer search or failed: keep the current options.
        }
    }

    return {
        projects,
        loading: computed(() => http.processing),
        load,
        search: debounce((title: string) => void load(title), 300),
    };
}
