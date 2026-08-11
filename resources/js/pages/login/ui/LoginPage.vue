<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { LogoutButton, LoginForm } from '@/features/auth';

const page = usePage();
</script>

<template>
    <Head title='Вхід' />

    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="mb-2 text-3xl font-light tracking-widest text-[#4457C2]">Project OS</h1>
                <p class="text-sm text-gray-500">
                    {{ page.props.auth.user ? 'Ви вже увійшли' : 'Увійдіть у свій акаунт' }}
                </p>
            </div>

            <div v-if="page.props.auth.user" class="space-y-6 text-center">
                <p class="text-gray-600">
                    Ви увійшли як
                    <span class="font-semibold">{{ page.props.auth.user.email }}</span>
                </p>
                <LogoutButton />
            </div>

            <LoginForm v-else />
        </div>
    </div>
</template>
