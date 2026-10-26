<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { RegisterForm, type RegisterInvitation } from '@/features/auth';

defineProps<{
    invitation: RegisterInvitation | null;
}>();
</script>

<template>
    <Head title='Реєстрація' />

    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="mb-2 text-3xl font-light tracking-widest text-[#4457C2]">Project OS</h1>
                <p class="text-sm text-gray-500">
                    {{ invitation ? 'Створіть новий аккаунт' : 'Реєстрація лише за запрошенням' }}
                </p>
            </div>

            <RegisterForm v-if="invitation" :invitation="invitation" />

            <div v-else class="space-y-6">
                <Message severity="warn">
                    Запрошення недійсне, прострочене або вже використане. Попросіть адміністратора надіслати нове.
                </Message>

                <Button :as="Link" variant="link" fluid href="/" label="Увійти" />
            </div>
        </div>
    </div>
</template>
