<script setup lang="ts">
  import Download from '@primeicons/vue/download';  
  import Times from '@primeicons/vue/times';
  import { formatSize } from '@/shared/lib';

  withDefaults(defineProps<{
    fileName: string;
    url: string;
    size: number;
    isUploaded?: boolean;
  }>(), {
    isUploaded: true
  });

  const emit = defineEmits(['onDownload', 'onRemove']);
</script>

<template>
    <div class="group relative rounded-lg overflow-hidden border border-surface-200 dark:border-surface-700">
        <img :src="url" :alt="fileName" class="w-full h-32 object-cover" />

        <div
            v-if="!isUploaded"
            class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider text-white bg-amber-500"
        >
            Очікує
        </div>

        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1">
            <Button
                iconOnly
                rounded
                severity="secondary"
                aria-label="Bookmark"
                size="small"
                @click="$emit('onDownload')"
            >
                <Download />
            </Button>
            <Button
                iconOnly
                rounded
                severity="danger"
                aria-label="Bookmark"
                size="small"
                @click="$emit('onRemove')"
            >
                <Times />
            </Button>
        </div>

        <div class="p-2">
            <div class="text-xs font-medium truncate">{{ fileName }}</div>
            <div class="text-xs text-muted-color">{{ formatSize(size) }}</div>
        </div>
    </div>
</template>