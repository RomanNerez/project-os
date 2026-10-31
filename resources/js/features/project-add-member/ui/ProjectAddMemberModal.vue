<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { getRoleLabel, MEMEBER_ROLE } from '@/entities/project';
import type { ProjectID } from '@/entities/project';
import { FormSelect, FormText } from '@/shared/ui';

const props = defineProps<{
    projectId: ProjectID;
}>();

const visible = defineModel<boolean>('visible', { required: true });

const roleOptions = Object.values(MEMEBER_ROLE)
    .map((role) => ({ label: getRoleLabel(role), value: role }));

const form = useForm({ email: '', role: MEMEBER_ROLE.EXECUTOR });

const submit = () => {
    form.post(route('projects.memeber', { id: props.projectId }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            visible.value = false;
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        header="Запросити учасника"
        :style="{ width: '32rem' }"
        :breakpoints="{ '640px': '95vw' }"
        @hide="close"
    >
        <form class="flex flex-col gap-4 pt-2" @submit.prevent="submit">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium">Email</label>
                <FormText v-model="form.email" placeholder="email@example.com" class="w-full" />
                <Message v-if="form.errors.email" severity="error" size="small" variant="simple">
                    {{ form.errors.email }}
                </Message>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium">Роль</label>
                <FormSelect
                    v-model="form.role"
                    :options="roleOptions"
                    optionLabel="label"
                    optionValue="value"
                    class="w-full"
                />
            </div>

            <div class="flex justify-end gap-2">
                <Button label="Скасувати" severity="secondary" text @click="visible = false" />
                <Button type="submit" icon="pi pi-user-plus" label="Додати" :loading="form.processing" />
            </div>
        </form>
    </Dialog>
</template>
