<script setup lang="ts">
import { ref, watch } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin, { type EventResizeDoneArg } from '@fullcalendar/interaction';
import ukLocale from '@fullcalendar/core/locales/uk';
import type {
    CalendarOptions,
    DateSelectArg,
    EventClickArg,
    EventDropArg,
    EventInput,
    EventSourceFuncArg,
} from '@fullcalendar/core';
import {
    eventColor,
    useCalendarEvents,
    useEventActions,
    type CalendarEvent,
    type EventPeriod,
} from '@/entities/event';

const props = defineProps<{
    /** Show only this project's events; null shows all visible ones. */
    projectId: number | null;
}>();

const emit = defineEmits<{
    select: [period: EventPeriod];
    open: [event: CalendarEvent];
}>();

const calendar = ref<InstanceType<typeof FullCalendar> | null>(null);

const { fetchEvents } = useCalendarEvents();
const { move } = useEventActions();

function toInput(event: CalendarEvent): EventInput {
    const color = eventColor(event);

    return {
        id: String(event.id),
        title: event.title,
        start: event.start_at,
        end: event.end_at ?? undefined,
        allDay: event.all_day,
        backgroundColor: color,
        borderColor: color,
        editable: event.permissions.update,
        classNames: event.my_status === 'declined' ? ['event-declined'] : event.my_status === 'pending' ? ['event-pending'] : [],
        extendedProps: { event },
    };
}

async function loadEvents(info: EventSourceFuncArg): Promise<EventInput[]> {
    try {
        return (await fetchEvents(info.startStr, info.endStr, props.projectId)).map(toInput);
    } catch {
        // Cancelled by a newer range (FullCalendar ignores stale fetches) or failed.
        return [];
    }
}

function onMove(info: EventDropArg | EventResizeDoneArg): void {
    const { event } = info;

    if (!event.start) {
        info.revert();
        return;
    }

    move(Number(event.id), { start: event.start, end: event.end, allDay: event.allDay }, {
        onError: () => info.revert(),
        onSuccess: () => refetch(),
    });
}

function onSelect(info: DateSelectArg): void {
    emit('select', { start: info.start, end: info.end, allDay: info.allDay });
    info.view.calendar.unselect();
}

function onClick(info: EventClickArg): void {
    info.jsEvent.preventDefault();
    emit('open', info.event.extendedProps.event as CalendarEvent);
}

function refetch(): void {
    calendar.value?.getApi().refetchEvents();
}

watch(() => props.projectId, refetch);

const options: CalendarOptions = {
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    locale: ukLocale,
    firstDay: 1,
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    },
    height: '100%',
    nowIndicator: true,
    dayMaxEvents: true,
    selectable: true,
    selectMirror: true,
    editable: true,
    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
    slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
    events: loadEvents,
    select: onSelect,
    eventClick: onClick,
    eventDrop: onMove,
    eventResize: onMove,
};

defineExpose({ refetch });
</script>

<template>
    <div class="event-calendar h-full">
        <FullCalendar ref="calendar" :options="options" />
    </div>
</template>

<style scoped>
.event-calendar {
    --fc-border-color: var(--p-content-border-color);
    --fc-page-bg-color: var(--p-content-background);
    --fc-neutral-bg-color: var(--p-content-hover-background);
    --fc-list-event-hover-bg-color: var(--p-content-hover-background);
    --fc-today-bg-color: color-mix(in srgb, var(--p-primary-color) 8%, transparent);
    --fc-now-indicator-color: var(--p-red-500);
    --fc-button-text-color: var(--p-primary-contrast-color);
    --fc-button-bg-color: var(--p-primary-color);
    --fc-button-border-color: var(--p-primary-color);
    --fc-button-hover-bg-color: var(--p-primary-hover-color);
    --fc-button-hover-border-color: var(--p-primary-hover-color);
    --fc-button-active-bg-color: var(--p-primary-active-color);
    --fc-button-active-border-color: var(--p-primary-active-color);
}

.event-calendar :deep(.fc-toolbar-title) {
    font-size: 1.125rem;
    font-weight: 600;
    text-transform: capitalize;
}

.event-calendar :deep(.fc-button) {
    font-size: 0.875rem;
}

.event-calendar :deep(.event-pending) {
    border-style: dashed;
    opacity: 0.85;
}

.event-calendar :deep(.event-declined) {
    opacity: 0.45;
    text-decoration: line-through;
}
</style>
