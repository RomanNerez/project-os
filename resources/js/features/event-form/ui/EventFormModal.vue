<script setup lang="ts">
import { computed, ref, toRef, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {
    eventColor,
    PARTICIPANT_STATUS_META,
    REMINDER_OPTIONS,
    TYPE_META,
    TYPE_OPTIONS,
    useEventActions,
    useEventForm,
    useParticipantOptions,
    type CalendarEvent,
    type EventPeriod,
} from '@/entities/event';
import { ProjectSelect } from '@/entities/project';
import { ConfirmModal, FormDate, FormMultiSelect, FormSelect, FormText, FormTextarea, UserAvatar } from '@/shared/ui';

const props = defineProps<{
    /** The event to edit or view; null creates a new one. */
    event: CalendarEvent | null;
    /** Where a new event starts, e.g. the selected calendar slot. */
    period?: EventPeriod | null;
    /** Project a new event starts in. */
    projectId?: number | null;
}>();

const emit = defineEmits(['onDone', 'onCancel']);

const visible = defineModel<boolean>('visible', { required: true });

const page = usePage();

const isEdit = computed(() => props.event !== null);
const canEdit = computed(() => !props.event || props.event.permissions.update);

const { form, submit, reset } = useEventForm(
    toRef(props, 'event'),
    computed(() => props.period ?? null),
    computed(() => props.projectId ?? null),
    () => emit('onDone'),
);

const { processing, remove, respond } = useEventActions();
const { options: participantOptions, loading: participantsLoading, load: loadParticipants } = useParticipantOptions();

const isDeleteOpen = ref(false);

// The author is never a participant of their own event.
const authorId = computed(() => props.event?.user_id ?? page.props.auth.user?.id);
const inviteOptions = computed(() => participantOptions.value.filter((user) => user.id !== authorId.value));

const pickerProps = computed(() => ({
    dateFormat: 'dd.mm.yy',
    showIcon: true,
    showTime: !form.all_day,
    hourFormat: '24' as const,
    stepMinute: 5,
}));

const color = computed({
    get: () => form.color?.replace('#', '') ?? eventColor({ color: null, type: form.type }).replace('#', ''),
    set: (value: string) => { form.color = `#${value}`; },
});

// Every opening starts from the event (or the selected slot), not from what was left in a closed dialog.
watch([visible, () => props.event], ([isVisible]) => {
    if (isVisible) reset();
}, { immediate: true });

// Participants must belong to the event's project, so switching it drops the others.
watch([visible, () => form.project_id], async ([isVisible, projectId]) => {
    if (!isVisible || !canEdit.value) return;

    if (await loadParticipants(projectId)) {
        const allowed = new Set(participantOptions.value.map((user) => user.id));
        form.participant_ids = form.participant_ids.filter((id) => allowed.has(id));
    }
}, { immediate: true });

const periodLabel = computed(() => {
    const event = props.event;
    if (!event) return '';

    const date = new Intl.DateTimeFormat('uk-UA', event.all_day
        ? { day: 'numeric', month: 'long', year: 'numeric' }
        : { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    const draftEnd = form.end_at && form.start_at && form.end_at.getTime() !== form.start_at.getTime() ? form.end_at : null;

    return [form.start_at, draftEnd].filter(Boolean).map((value) => date.format(value as Date)).join(' — ');
});

function cancel(): void {
    reset();
    emit('onCancel');
}

function confirmDelete(): void {
    if (!props.event) return;

    remove(props.event.id, {
        onSuccess: () => {
            isDeleteOpen.value = false;
            emit('onDone');
        },
    });
}

function answer(status: 'accepted' | 'declined'): void {
    if (props.event) respond(props.event.id, status, { onSuccess: () => emit('onDone') });
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :header="!canEdit ? 'Подія' : isEdit ? 'Редагувати подію' : 'Нова подія'"
        :style="{ width: '40rem' }"
        :breakpoints="{ '640px': '95vw' }"
    >
        <!-- Read-only view for participants and project members who cannot edit. -->
        <div v-if="!canEdit && props.event" class="flex flex-col gap-4">
            <div class="flex items-start gap-3">
                <span class="mt-1 size-3 shrink-0 rounded-full" :style="{ background: eventColor(props.event) }" />
                <div class="min-w-0">
                    <h3 class="text-lg font-semibold">{{ props.event.title }}</h3>
                    <p class="text-sm text-muted-color">{{ periodLabel }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <Tag :value="TYPE_META[props.event.type].label" :icon="TYPE_META[props.event.type].icon" severity="secondary" />
                <Tag v-if="props.event.project" :value="props.event.project.title" icon="pi pi-folder" severity="info" />
            </div>

            <p v-if="props.event.location" class="flex items-center gap-2 text-sm">
                <i class="pi pi-map-marker text-muted-color" />{{ props.event.location }}
            </p>
            <p v-if="props.event.description" class="whitespace-pre-line text-sm">{{ props.event.description }}</p>
        </div>

        <div v-else class="flex flex-col gap-4">
            <FormText
                v-model="form.title"
                autofocus
                fluid
                label="Назва"
                placeholder="Напр., Демо для клієнта"
                :message="form.errors.title"
            />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormSelect
                    v-model="form.type"
                    label="Тип"
                    :options="TYPE_OPTIONS"
                    option-label="label"
                    option-value="value"
                    :message="form.errors.type"
                />
                <ProjectSelect
                    v-model="form.project_id"
                    label="Проєкт"
                    placeholder="Особиста подія"
                    show-clear
                    :selected="props.event?.project"
                    :message="form.errors.project_id"
                />
            </div>

            <div class="flex items-center gap-2">
                <ToggleSwitch v-model="form.all_day" input-id="event-all-day" />
                <Label for="event-all-day">Весь день</Label>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormDate
                    v-model="form.start_at"
                    name="event-start"
                    :label="form.all_day ? 'З дати' : 'Початок'"
                    :input-props="pickerProps"
                    :message="form.errors.start_at"
                />
                <FormDate
                    v-model="form.end_at"
                    name="event-end"
                    :label="form.all_day ? 'По дату' : 'Кінець'"
                    :input-props="{ ...pickerProps, showButtonBar: true }"
                    :message="form.errors.end_at"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormText
                    v-model="form.location"
                    fluid
                    label="Місце"
                    placeholder="Офіс або посилання на зустріч"
                    :message="form.errors.location"
                />
                <FormSelect
                    v-model="form.reminder_minutes"
                    label="Нагадування на email"
                    :options="REMINDER_OPTIONS"
                    option-label="label"
                    option-value="value"
                    :message="form.errors.reminder_minutes"
                />
            </div>

            <FormMultiSelect
                v-model="form.participant_ids"
                label="Учасники"
                :placeholder="form.project_id ? 'Учасники проєкту' : 'Колеги зі спільних проєктів'"
                :options="inviteOptions"
                option-label="name"
                option-value="id"
                display="chip"
                filter
                :loading="participantsLoading"
                :message="form.errors.participant_ids"
            />

            <FormTextarea
                v-model="form.description"
                label="Опис"
                placeholder="Порядок денний, нотатки"
                :rows="3"
                :message="form.errors.description"
            />

            <div class="flex items-center gap-3">
                <Label>Колір</Label>
                <ColorPicker v-model="color" />
                <Button
                    v-if="form.color"
                    type="button"
                    label="Як у типу"
                    size="small"
                    severity="secondary"
                    text
                    @click="form.color = null"
                />
            </div>
        </div>

        <section v-if="props.event?.participants.data.length" class="mt-4 flex flex-col gap-2">
            <Label>Відповіді учасників</Label>
            <ul class="flex flex-col gap-2">
                <li
                    v-for="participant in props.event.participants.data"
                    :key="participant.id"
                    class="flex items-center justify-between gap-2 text-sm"
                >
                    <span class="flex min-w-0 items-center gap-2">
                        <UserAvatar :name="participant.name" :image="participant.avatar_url" />
                        <span class="truncate">{{ participant.name }}</span>
                    </span>
                    <Tag
                        :value="PARTICIPANT_STATUS_META[participant.status].label"
                        :severity="PARTICIPANT_STATUS_META[participant.status].severity"
                    />
                </li>
            </ul>
        </section>

        <template #footer>
            <div class="mt-2 flex w-full flex-wrap items-center justify-between gap-2">
                <Button
                    v-if="props.event?.permissions.delete"
                    type="button"
                    label="Видалити"
                    icon="pi pi-trash"
                    severity="danger"
                    text
                    :disabled="form.processing"
                    @click="isDeleteOpen = true"
                />
                <span v-else />

                <div class="flex flex-wrap justify-end gap-2">
                    <template v-if="props.event?.my_status">
                        <Button
                            type="button"
                            label="Відхилити"
                            icon="pi pi-times"
                            severity="secondary"
                            :outlined="props.event.my_status !== 'declined'"
                            :loading="processing"
                            @click="answer('declined')"
                        />
                        <Button
                            type="button"
                            label="Прийняти"
                            icon="pi pi-check"
                            severity="success"
                            :outlined="props.event.my_status !== 'accepted'"
                            :loading="processing"
                            @click="answer('accepted')"
                        />
                    </template>

                    <template v-if="canEdit">
                        <Button
                            type="button"
                            label="Скасувати"
                            severity="secondary"
                            text
                            :disabled="form.processing"
                            @click="cancel"
                        />
                        <Button
                            type="button"
                            :label="isEdit ? 'Зберегти' : 'Створити'"
                            :loading="form.processing"
                            @click="submit"
                        />
                    </template>
                    <Button v-else type="button" label="Закрити" severity="secondary" text @click="cancel" />
                </div>
            </div>
        </template>

        <ConfirmModal
            v-model:visible="isDeleteOpen"
            header="Видалити подію?"
            :message="`Подію «${props.event?.title}» буде видалено для всіх учасників.`"
            icon="pi pi-exclamation-triangle"
            severity="danger"
            confirm-label="Видалити"
            :processing="processing"
            @confirm="confirmDelete"
            @cancel="isDeleteOpen = false"
        />
    </Dialog>
</template>
