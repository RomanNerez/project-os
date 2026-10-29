<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { getInitials } from '@/shared/lib';

interface Props {
    name?: string;
    image?: string | null;
}

const props = defineProps<Props>()

const userInitials = computed(() => getInitials(props.name ?? ''));

const imageFailed = ref(false);
watch(() => props.image, () => { imageFailed.value = false; });

const avatarImage = computed(() => (props.image && !imageFailed.value ? props.image : undefined));
</script>

<template>
    <Avatar
        :image="avatarImage"
        :label="avatarImage ? undefined : userInitials"
        shape="circle"
        @error="imageFailed = true"
    />
</template>
