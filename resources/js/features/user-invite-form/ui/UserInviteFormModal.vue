<script setup lang="ts">
import { ref, watch } from 'vue';
import { useInviteUserForm } from '@/entities/user';
import { FormText } from '@/shared/ui';

const emit = defineEmits(['onDone', 'onCancel']);

const visible = defineModel<boolean>('visible', { required: true });

const sentTo = ref<string | null>(null);

const { form, submit, reset } = useInviteUserForm((email) => {
    sentTo.value = email;
    reset();
});

watch(visible, (isVisible) => {
    if (isVisible) {
        sentTo.value = null;
        reset();
    }
});

function close(): void {
    reset();
    emit(sentTo.value ? 'onDone' : 'onCancel');
}
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        header="Запросити користувача"
        :style="{ width: '28rem' }"
        :breakpoints="{ '640px': '95vw' }"
    >
        <Message v-if="sentTo" severity="success" class="mb-4">
            Запрошення надіслано на {{ sentTo }}
        </Message>

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <p class="text-sm text-muted-color">
                Ми надішлемо на цей email одноразове посилання для реєстрації.
            </p>

            <FormText
                v-model="form.email"
                autofocus
                fluid
                name="email"
                label="Email"
                placeholder="name@example.com"
                :message="form.errors.email"
            />

            <button type="submit" class="hidden" />
        </form>

        <template #footer>
            <div class="mt-2 flex justify-end gap-2">
                <Button
                    type="button"
                    :label="sentTo ? 'Готово' : 'Скасувати'"
                    severity="secondary"
                    text
                    :disabled="form.processing"
                    @click="close"
                />
                <Button
                    type="button"
                    label="Надіслати запрошення"
                    icon="pi pi-send"
                    :loading="form.processing"
                    @click="submit"
                />
            </div>
        </template>
    </Dialog>
</template>
