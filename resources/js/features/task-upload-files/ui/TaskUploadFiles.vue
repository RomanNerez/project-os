<script setup lang="ts">
  import { ref, computed, type VNodeRef } from 'vue';
  import Plus from '@primeicons/vue/plus';
  import Upload from '@primeicons/vue/upload';
  import { updateTaskFiles, type TaskID } from '@/entities/task';
  import { MediaPreview } from '@/entities/media';
  import { type FileItem } from '../model/types';
  import { EmptyList } from '@/shared/ui';
  import type { FileUploadSelectEvent, FileUploadUploaderEvent } from 'primevue/fileupload';

  interface Props {
    taskId: TaskID;
  }

  const props = defineProps<Props>();
  
  const fu = ref<VNodeRef | null>(null);
  const pendingFiles = ref<File[]>([]);
  
  const allMedia = computed<FileItem[]>(() => pendingFiles.value.map(file => ({
    id: Date.now() + Math.random(),
    file: file,
    name: file.name,
    size: file.size,
    mimeType: file.type,
    url: URL.createObjectURL(file),
  })));
  
  const pendingFilesCount = computed(() => pendingFiles.value.length);

  const onSelect = (event: FileUploadSelectEvent) => {
    pendingFiles.value = event.files;
  };

  const onUpload = (event: FileUploadUploaderEvent) => {
    updateTaskFiles(props.taskId, pendingFiles.value, {
      onSuccess: () => {
        pendingFiles.value = [];
        fu.value.clear();
      } 
    });
  };
  
  const removeItem = (item: FileItem) => {
    const indexInPending = pendingFiles.value.findIndex(f => f === item.file);

    if (indexInPending !== -1) {
      fu.value.remove(null, indexInPending);
      pendingFiles.value.splice(indexInPending, 1);
    }
  };
</script>

<template>
    <FileUpload
      ref="fu"
      name="files[]"
      :multiple="true"
      mode="advanced"
      :pt="{
        root: { class: '!border-0 flex flex-col gap-2' },
        header: { class: '!p-0' },
        content: { class: '!p-0' }
      }"
      customUpload
      @select="onSelect"
      @uploader="onUpload"
    >
      <template #header="{ uploadCallback }">
        <div class="flex items-center gap-2">
          <Button type="button" severity="secondary" variant="outlined" @click="fu.choose()">
            <Plus />
            Додати Файл
          </Button>
          <Button v-if="pendingFilesCount > 0" type="button" @click="uploadCallback">
            <Upload />
            Завантажити додані файли ({{ pendingFilesCount }})
          </Button>
        </div>
      </template>
  
      <template #content>
        <div v-if="allMedia.length > 0">
          <div class="grid grid-flow-col auto-cols-[120px] gap-2 overflow-x-auto w-full">
            <MediaPreview
              v-for="(item, i) of allMedia" 
              :key="item.id"
              :file-name="item.name"
              :url="item.url"
              :size="item.size"
              :is-uploaded="false"
              @on-remove="removeItem(item)"
            />
          </div>
        </div>
      </template>
  
      <template #empty>
        <EmptyList
          v-if="allMedia.length === 0"
          class="py-10"
          decription="Зображення ще не додано чи завантажено."
          :show-action="false"
        />
      </template>
    </FileUpload>
</template>