<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';

function logout(): void {
    router.post('/logout');
}
</script>

<template>
    <Button
        label="Вийти"
        icon="pi pi-sign-out"
        severity="secondary"
        @click="logout"
    />
</template>
