<script setup lang="ts">
import { useMediaDelete } from '@/entities/media';
import type { MediaID } from '@/entities/media/model/types';
import { ConfirmModal } from '@/shared/ui';

const props = defineProps<{
    mediaId: MediaID;
    fileName: string;
}>();

const visible = defineModel<boolean>('visible', { required: true });
const emit = defineEmits(['onDone', 'onCancel']);

const { isDeleting, destroy } = useMediaDelete();

function remove() {
    destroy(props.mediaId, {
        onSuccess: () => emit('onDone')
    });
}
</script>

<template>
    <ConfirmModal
        v-model:visible="visible"
        header="Видалити файл"
        icon="pi pi-exclamation-triangle"
        severity="danger"
        confirm-label="Видалити"
        :processing="isDeleting"
        @confirm="remove"
        @cancel="$emit('onCancel')"
    >
        Ви впевнені, що хочете видалити файл
        <span class="font-semibold">«{{ fileName }}»</span>?
        Цю дію неможливо скасувати.
    </ConfirmModal>
</template>
