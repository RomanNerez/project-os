<script setup lang="ts">
    interface Props {
        iconClass?: string;
        decription: string;
        buttonLabel?: string;
        showAction?: boolean;
    }

    withDefaults(defineProps<Props>(), {
        showAction: true,
    })
    defineEmits(['onHandler'])
</script>

<template>
    <div class="flex flex-col items-center gap-3 rounded-lg border border-dashed border-surface-300 py-16 dark:border-surface-600">
        <i v-if="iconClass" class="pi text-3xl text-muted-color" :class="iconClass"></i>
        <p class="text-muted-color">{{ decription }}</p>
        <Button v-if="showAction" icon="pi pi-plus" text :label="buttonLabel" @click="$emit('onHandler')" />
    </div>
</template>
