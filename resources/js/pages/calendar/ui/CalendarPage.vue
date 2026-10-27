<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { WorkspaceLayout } from '@/widgets/workspace-layout';
import { EventCalendar } from '@/widgets/event-calendar';
import { EventFormModal } from '@/features/event-form';
import { ProjectSelect } from '@/entities/project';
import type { CalendarEvent, EventPeriod } from '@/entities/event';

const calendar = ref<InstanceType<typeof EventCalendar> | null>(null);

const projectId = ref<number | null>(null);
const isFormOpen = ref(false);
const selected = ref<CalendarEvent | null>(null);
const period = ref<EventPeriod | null>(null);

function openCreate(range: EventPeriod | null = null): void {
    selected.value = null;
    period.value = range;
    isFormOpen.value = true;
}

function openEvent(event: CalendarEvent): void {
    selected.value = event;
    isFormOpen.value = true;
}

function close(): void {
    isFormOpen.value = false;
}

function onDone(): void {
    close();
    calendar.value?.refetch();
}
</script>

<template>
    <Head title="Календар" />

    <WorkspaceLayout
        active="calendar"
        description="Зустрічі, дедлайни та нагадування — особисті й ваших проєктів"
    >
        <template #actions>
            <ProjectSelect
                v-model="projectId"
                placeholder="Усі проєкти"
                show-clear
                :fluid="false"
                class="w-64"
            />
            <Button label="Нова подія" icon="pi pi-plus" @click="openCreate()" />
        </template>

        <div class="h-full rounded-lg border border-[var(--p-content-border-color)] bg-[var(--p-content-background)] p-4 shadow-sm">
            <EventCalendar
                ref="calendar"
                :project-id="projectId"
                @select="openCreate"
                @open="openEvent"
            />
        </div>

        <EventFormModal
            v-model:visible="isFormOpen"
            :event="selected"
            :period="period"
            :project-id="projectId"
            @on-done="onDone"
            @on-cancel="close"
        />
    </WorkspaceLayout>
</template>
