<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { ForgotPasswordForm } from '@/features/auth';

const page = usePage();
</script>

<template>
    <Head title='Відновлення пароля' />

    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="mb-2 text-3xl font-light tracking-widest text-[#4457C2]">Project OS</h1>
                <p class="text-sm text-gray-500">
                    Вкажіть email, і ми надішлемо посилання для скидання пароля
                </p>
            </div>

            <Message v-if="page.flash.status" severity="success" :closable="false" class="mb-6">
                {{ page.flash.status }}
            </Message>

            <ForgotPasswordForm />
        </div>
    </div>
</template>
