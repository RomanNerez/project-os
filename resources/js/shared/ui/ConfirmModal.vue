<script setup lang="ts">
import { computed } from 'vue';

type Severity = 'primary' | 'secondary' | 'success' | 'info' | 'warn' | 'help' | 'danger' | 'contrast';

const props = withDefaults(defineProps<{
    header: string;
    message?: string;
    icon?: string;
    severity?: Severity;
    confirmLabel?: string;
    cancelLabel?: string;
    processing?: boolean;
}>(), {
    message: undefined,
    icon: undefined,
    severity: 'primary',
    confirmLabel: 'Підтвердити',
    cancelLabel: 'Скасувати',
    processing: false,
});

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();

const visible = defineModel<boolean>('visible', { required: true });

const iconColor = computed(() => {
    switch (props.severity) {
        case 'danger':
            return 'text-red-500';
        case 'warn':
            return 'text-amber-500';
        default:
            return 'text-primary';
    }
});

function cancel(): void {
    emit('cancel');
    visible.value = false;
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :header="header"
        :closable="false"
        class="w-full max-w-md"
        @hide="cancel"
    >
        <div class="flex items-start gap-3">
            <i v-if="icon" :class="[icon, iconColor]" class="text-2xl"></i>
            <div class="text-sm">
                <slot>{{ message }}</slot>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <Button
                type="button"
                :label="cancelLabel"
                severity="secondary"
                text
                :disabled="processing"
                @click="cancel"
            />
            <Button
                type="button"
                :label="confirmLabel"
                :severity="severity"
                :loading="processing"
                @click="emit('confirm')"
            />
        </div>
    </Dialog>
</template>
