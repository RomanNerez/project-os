<script setup lang="ts">
import { useCommentDelete, type CommentID } from '@/entities/comment';
import { ConfirmModal } from '@/shared/ui';

const props = defineProps<{
    commentId: CommentID;
}>();

const visible = defineModel<boolean>('visible', { required: true });
const emit = defineEmits(['onDone', 'onCancel']);

const { isDeleting, destroy } = useCommentDelete();

function remove() {
    destroy(props.commentId, {
        onSuccess: () => emit('onDone')
    });
}
</script>

<template>
    <ConfirmModal
        v-model:visible="visible"
        header="Видалити комментар"
        icon="pi pi-exclamation-triangle"
        severity="danger"
        confirm-label="Видалити"
        :processing="isDeleting"
        @confirm="remove"
        @cancel="$emit('onCancel')"
    >
        Ви впевнені, що хочете видалити цей комментар?
        Цю дію неможливо скасувати.
    </ConfirmModal>
</template>
