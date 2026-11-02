<script setup lang="ts">
import { watch } from 'vue';
import { getRoleLabel, MEMEBER_ROLE, useProjectMemberUpdate } from '@/entities/project';
import type { ProjectID, ProjectMember } from '@/entities/project';
import { FormNumber, FormSelect } from '@/shared/ui';

const props = defineProps<{
    projectId: ProjectID;
    member: ProjectMember | null;
}>();

const visible = defineModel<boolean>('visible', { required: true });
const emit = defineEmits<{
    onDone: [];
    onCancel: [];
}>();

const roleOptions = Object.values(MEMEBER_ROLE)
    .map((role) => ({ label: getRoleLabel(role), value: role }));

const { form, fill, update } = useProjectMemberUpdate();

watch(() => props.member, (member) => {
    if (member) fill(member);
}, { immediate: true });

const submit = () => {
    if (!props.member) return;

    update(props.projectId, props.member.id, {
        preserveScroll: true,
        onSuccess: () => emit('onDone'),
    });
};
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :header="`Редагувати учасника «${member?.name ?? ''}»`"
        :style="{ width: '32rem' }"
        :breakpoints="{ '640px': '95vw' }"
        @hide="emit('onCancel')"
    >
        <form class="flex flex-col gap-4 pt-2" @submit.prevent="submit">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium">Роль</label>
                <FormSelect
                    v-model="form.role"
                    :options="roleOptions"
                    optionLabel="label"
                    optionValue="value"
                    class="w-full"
                />
                <Message v-if="form.errors.role" severity="error" size="small" variant="simple">
                    {{ form.errors.role }}
                </Message>
            </div>

            <FormNumber
                v-model="form.hourly_rate"
                label="Ставка за годину"
                label-for="edit-member-hourly-rate"
                placeholder="Не вказано"
                :min="0"
                mode="currency"
                currency="USD"
                locale="en-US"
                :message="form.errors.hourly_rate"
            />

            <div class="flex justify-end gap-2">
                <Button label="Скасувати" severity="secondary" text @click="visible = false" />
                <Button type="submit" icon="pi pi-check" label="Зберегти" :loading="form.processing" />
            </div>
        </form>
    </Dialog>
</template>
