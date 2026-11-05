<script setup lang="ts">
import { formatDurationHuman } from '@/shared/lib';
import { UserAvatar } from '@/shared/ui';

const props = defineProps<{
    name: string;
    avatar_url: string | null;
    totalSeconds: number;
    disabled?: boolean;
}>();

const seconds = defineModel<number>({ required: true });
</script>

<template>
    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
            <UserAvatar :name="name" :image="avatar_url" class="shrink-0" />
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium">{{ name }}</p>
                <p class="text-xs text-muted-color">Затрекано {{ formatDurationHuman(totalSeconds) }}</p>
            </div>
        </div>

        <div class="px-2">
            <Slider
                :model-value="seconds"
                :min="0"
                :max="totalSeconds"
                :disabled="props.disabled"
                :aria-label="`Години до оплати: ${name}`"
                @update:model-value="(seconds = $event as number)"
            />
        </div>

        <div class="flex justify-between text-xs tabular-nums">
            <span class="font-semibold">{{ formatDurationHuman(seconds) }}</span>
            <span class="text-muted-color">з {{ formatDurationHuman(totalSeconds) }}</span>
        </div>
    </div>
</template>
