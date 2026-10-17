<script setup lang="ts">
import { computed } from 'vue';
import { getInitials } from '@/shared/lib';

interface Props {
    name?: string;
    image?: string | null;
}

const props = defineProps<Props>()

const userInitials = computed(() => getInitials(props.name ?? ''));
</script>

<template>
    <Avatar v-if="image" :image="image" shape="circle" />
    <Avatar v-else :label="userInitials" shape="circle" />
</template>
