<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ResetPasswordForm } from '@/features/auth';

defineProps<{
    token: string;
    email: string;
}>();
</script>

<template>
    <Head title='Новий пароль' />

    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="mb-2 text-3xl font-light tracking-widest text-[#4457C2]">Project OS</h1>
                <p class="text-sm text-gray-500">Придумайте новий пароль</p>
            </div>

            <ResetPasswordForm :token="token" :email="email" />
        </div>
    </div>
</template>
