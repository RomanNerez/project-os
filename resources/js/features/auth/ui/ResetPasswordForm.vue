<script setup lang="ts">
import { FormPassword, FormText } from '@/shared/ui';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    token: string;
    email: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit(): void {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <Message v-if="form.errors.token" severity="error" :closable="false">
            {{ form.errors.token }}
        </Message>

        <FormText
            v-model="form.email"
            label="Email"
            name="email"
            type="email"
            :disabled="Boolean(email)"
            :message="form.errors.email"
        />

        <FormPassword
            v-model="form.password"
            label="Новий пароль"
            name="password"
            placeholder="••••••••"
            :message="form.errors.password"
        />

        <FormPassword
            v-model="form.password_confirmation"
            label="Повторіть пароль"
            name="password_confirmation"
            placeholder="••••••••"
        />

        <Button
            type="submit"
            label="Змінити пароль"
            icon="pi pi-lock"
            :loading="form.processing"
            fluid
        />

        <Button
            v-if="form.errors.token"
            :as="Link"
            variant="link"
            fluid
            href="/forgot-password"
            label="Запросити нове посилання"
        />
    </form>
</template>
