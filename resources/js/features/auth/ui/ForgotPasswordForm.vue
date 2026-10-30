<script setup lang="ts">
import { FormText } from '@/shared/ui';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
});

function submit(): void {
    form.post('/forgot-password', {
        preserveScroll: true,
    });
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <FormText
            v-model="form.email"
            label="Email"
            name="email"
            type="email"
            placeholder="you@example.com"
            :message="form.errors.email"
            :input-props="{ autocomplete: 'email', autofocus: true, required: true }"
        />

        <Button
            type="submit"
            label="Надіслати посилання"
            icon="pi pi-envelope"
            :loading="form.processing"
            fluid
        />

        <Button
            :as="Link"
            variant="link"
            fluid
            href="/"
            label="Повернутися до входу"
        />
    </form>
</template>
