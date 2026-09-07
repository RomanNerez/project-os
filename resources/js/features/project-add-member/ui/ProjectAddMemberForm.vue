<script setup lang="ts">
import { getRoleLabel, MEMEBER_ROLE } from '@/entities/project';
import { FormSelect, FormText } from '@/shared/ui';
import { useForm } from '@inertiajs/vue3';

const roleOptions = Object.values(MEMEBER_ROLE)
    .map((role) => ({ label: getRoleLabel(role), value: role }));

const form = useForm({ email: '', role: MEMEBER_ROLE.MEMBER })

const submit = () => {
    route('projects.index')
}
</script>

<template>
    <div class="flex flex-col sm:flex-row gap-2">
        <div class="flex-1">
            <FormText
                v-model="form.email"
                placeholder="email@example.com"
                class="w-full"
            />
        </div>

        <FormSelect
            v-model="form.role"
            :options="roleOptions"
            optionLabel="label"
            optionValue="value"
            class="w-full sm:w-44"
        />

        <Button
            type="submit"
            icon="pi pi-user-plus"
            label="Додати"
            :loading="form.processing"
            class="shrink-0"
            @click="submit"
        />

        <Message v-if="form.errors.email" severity="error" size="small" variant="simple">
          {{ form.errors.email }}
        </Message>
    </div>
</template>
