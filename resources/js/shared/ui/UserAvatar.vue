<script setup lang="ts">
import { computed } from 'vue';
import { getInitials } from '@/shared/lib';

interface Props {
    name?: string;
}

const props = defineProps<Props>()

const userInitials = computed(() => getInitials(props.name ?? ''));
</script>

<template>
    <Avatar :label="userInitials" shape="circle" />
</template>