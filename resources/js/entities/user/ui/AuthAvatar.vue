<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import UserAvatar from './UserAvatar.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const name = computed(() => user.value?.name ?? '');
</script>

<template>
    <UserAvatar :name="name" />
</template>