<script setup lang="ts">
import Button from 'primevue/button'
import Textarea from 'primevue/textarea'
import { AuthAvatar } from '@/entities/user'
import { useTaskCommentForm, type TaskID } from '@/entities/task';

interface Props {
  taskId: TaskID;
}

const props = defineProps<Props>();

const { form, submit } = useTaskCommentForm()

const addComment = () => {
  submit(props.taskId, {
    onSuccess: () => form.reset()
  })
}
</script>

<template>
  <div class="flex gap-4">
    <AuthAvatar size="large" />
    <div class="flex-1 space-y-3">
      <Textarea 
        v-model="form.body" 
        rows="3" 
        placeholder="Напишіть ваш коментар..." 
        class="w-full resize-none p-3 border-gray-200 focus:border-primary-500 rounded-lg"
        autoResize
      />
      <div class="flex justify-end">
        <Button 
          label="Надіслати" 
          icon="pi pi-send" 
          :disabled="!form.body.trim()"
          @click="addComment" 
          size="small"
        />
      </div>
    </div>
  </div>
</template>