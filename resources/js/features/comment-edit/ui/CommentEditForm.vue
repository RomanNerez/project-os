<script setup lang="ts">
import type { CommentID } from '@/entities/comment';
import { useCommentUpdate } from '@/entities/comment/model/useCommentUpdate';

interface Props {
    commentId: CommentID;
    body: string;
}

const props = defineProps<Props>()

const emit = defineEmits(['onDone', 'onCancel']);
const {form, update} = useCommentUpdate(props.body);

const updateComment = () => {
    update(props.commentId, {
        onSuccess: () => {
            emit('onDone')
        }
    })
}
</script>

<template>
    <div class="space-y-3 mt-2">
        <Textarea 
            v-model="form.body" 
            rows="2" 
            class="w-full resize-none p-2 border-gray-300 rounded-md text-sm"
            autoResize
        />

        <div class="flex justify-end gap-2">
            <Button 
                label="Скасувати" 
                severity="secondary" 
                text 
                size="small"
                @click="$emit('onCancel')"
            />
            <Button 
                label="Зберегти"
                icon="pi pi-check"
                size="small"
                :loading="form.processing"
                :disabled="!form.body.trim()"
                @click="updateComment"
            />
        </div>
    </div>
</template>
