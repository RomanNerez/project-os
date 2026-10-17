<script setup lang="ts">
import { FormPassword } from '@/shared/ui';
import { useUpdatePassword } from '../model/useUpdatePassword';

const { form, submit } = useUpdatePassword();
</script>

<template>
    <form class="flex max-w-md flex-col gap-4" @submit.prevent="submit">
        <FormPassword
            v-model="form.current_password"
            name="current_password"
            label="Поточний пароль"
            placeholder="••••••••"
            :message="form.errors.current_password"
        />
        <FormPassword
            v-model="form.new_password"
            name="new_password"
            label="Новий пароль"
            placeholder="••••••••"
            :message="form.errors.new_password"
        />
        <FormPassword
            v-model="form.new_password_confirmation"
            name="new_password_confirmation"
            label="Повторіть новий пароль"
            placeholder="••••••••"
            :message="form.errors.new_password_confirmation"
        />

        <div class="flex items-center gap-3">
            <Button type="submit" label="Змінити пароль" icon="pi pi-lock" :loading="form.processing" />
            <Message v-if="form.recentlySuccessful" severity="success" size="small" variant="simple">
                Пароль змінено
            </Message>
        </div>
    </form>
</template>
