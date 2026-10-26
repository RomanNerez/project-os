<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit(): void {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <Message v-if="form.errors.email" severity="error" :closable="false">
            {{ form.errors.email }}
        </Message>

        <div class="flex flex-col gap-2">
            <label for="email" class="text-sm font-medium text-gray-700">Email</label>
            <InputText
                id="email"
                v-model="form.email"
                type="email"
                placeholder="you@example.com"
                autocomplete="username"
                autofocus
                required
                :invalid="Boolean(form.errors.email)"
                fluid
            />
        </div>

        <div class="flex flex-col gap-2">
            <label for="password" class="text-sm font-medium text-gray-700">Пароль</label>
            <Password
                v-model="form.password"
                input-id="password"
                placeholder="Введіть пароль"
                autocomplete="current-password"
                required
                :feedback="false"
                toggle-mask
                :invalid="Boolean(form.errors.password)"
                fluid
            />
            <small v-if="form.errors.password" class="text-red-500">
                {{ form.errors.password }}
            </small>
        </div>

        <div class="flex items-center gap-2">
            <Checkbox v-model="form.remember" input-id="remember" binary />
            <label for="remember" class="text-sm text-gray-600">Запам'ятати мене</label>
        </div>

        <Button
            type="submit"
            label="Увійти"
            icon="pi pi-sign-in"
            :loading="form.processing"
            fluid
        />
    </form>
</template>
