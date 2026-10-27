import type { EventID } from '../model/types';

export const eventRoutes = {
    index: () => '/calendar',
    list: () => '/calendar/events',
    participants: () => '/calendar/participants',
    store: () => '/calendar/events',
    update: (id: EventID) => `/calendar/events/${id}`,
    move: (id: EventID) => `/calendar/events/${id}/move`,
    destroy: (id: EventID) => `/calendar/events/${id}`,
    respond: (id: EventID) => `/calendar/events/${id}/respond`,
};
