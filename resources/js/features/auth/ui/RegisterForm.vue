<script setup lang="ts">
import { FormDate, FormPassword, FormRadioGroup, FormText } from '@/shared/ui';
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import type { RegisterInvitation } from '../model/types';

const props = defineProps<{
    invitation: RegisterInvitation;
}>();

const form = useForm({
    token: props.invitation.token,
    first_name: '',
    last_name: '',
    password: '',
    password_confirmation: '',
    gender: '',
    birth: null,
});

// Not a form field (the email comes from the invitation), but the server can still reject it.
const emailError = computed(() => (form.errors as Partial<Record<string, string>>).email);

function submit(): void {
    form.post('/register');
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <FormText
                v-model="form.first_name"
                label="Імʼя"
                placeholder="Тарас"
                :message="form.errors.first_name"
            />

            <FormText
                v-model="form.last_name"
                label="Прізвище"
                placeholder="Шевченко"
                :message="form.errors.last_name"
            />
        </div>

        <Message v-if="form.errors.token" severity="error">
            {{ form.errors.token }}
        </Message>

        <FormText
            :model-value="invitation.email"
            label="Email"
            name="email"
            disabled
            :message="emailError"
        />

        <FormPassword
            v-model="form.password"
            label="Пароль"
            placeholder="••••••••"
            :message="form.errors.password"
        />

        <FormPassword
            v-model="form.password_confirmation"
            label="Повторіть пароль"
            placeholder="••••••••"
        />

        <FormRadioGroup
            name="gender"
            v-model="form.gender"
            label="Пол"
            :options="[
                {
                    label: 'Чоловік',
                    value: 'male'
                },
                {
                    label: 'Жінка',
                    value: 'female'
                },
            ]"
            :message="form.errors.gender"
        />

        <FormDate
            v-model="form.birth"
            label="День народження"
            placeholder="дд-мм-рр"
            mask="99-99-9999"
            :message="form.errors.birth"
            :inputProps="{
                dateFormat: 'dd-mm-yy',
                updateModelType: 'string'
            }"
        />

        <Button
            type="submit"
            label="Зареєструватися"
            icon="pi pi-sign-up"
            :loading="form.processing"
            fluid
        />

        <Button
            :as="Link"
            variant="link"
            fluid
            href="/"
            label="Увійти"
        />
    </form>
    
</template>
