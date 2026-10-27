import { useHttp } from '@inertiajs/vue3';
import { buildSearchParam, SEARCH_JOIN } from '@/shared/lib';
import { eventRoutes } from '../api/eventRoutes';
import type { CalendarEvent } from './types';

interface CalendarEventsQuery {
    start: string;
    end: string;
    search: string;
    searchJoin: typeof SEARCH_JOIN.AND;
}

/** Events of a range (optionally of one project) as FullCalendar's event source asks for them. */
export function useCalendarEvents() {
    const http = useHttp<CalendarEventsQuery, { data: CalendarEvent[] }>({
        start: '',
        end: '',
        search: '',
        searchJoin: SEARCH_JOIN.AND,
    });

    async function fetchEvents(start: string, end: string, projectId: number | null): Promise<CalendarEvent[]> {
        http.cancel();
        http.start = start;
        http.end = end;
        http.search = projectId ? buildSearchParam({ project_id: String(projectId) }) : '';

        const response = await http.get(eventRoutes.list());

        return response?.data ?? [];
    }

    return { fetchEvents };
}
