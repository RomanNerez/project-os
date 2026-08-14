<script setup lang="ts">
import { STATUS_OPTIONS, type Project, useProjectForm } from '@/entities/project';
import { ref } from 'vue';

const props = defineProps<{
    project: Project | null;
}>();

const project = ref(props.project);
const visible = defineModel<boolean>('visible', { required: true });

const {form, submit} = useProjectForm(project, () => { visible.value = false; })
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :header="project ? 'Редагувати проєкт' : 'Новий проєкт'"
        class="w-full max-w-lg"
    >
        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="flex flex-col gap-2">
                <label for="project-name" class="text-sm font-medium">Назва</label>
                <InputText id="project-name" v-model="form.title" placeholder="Назва проєкту" autofocus fluid />
            </div>

            <div class="flex flex-col gap-2">
                <label for="project-description" class="text-sm font-medium">Опис</label>
                <Textarea id="project-description" v-model="form.description" rows="3" placeholder="Короткий опис проєкту" auto-resize fluid />
            </div>

            <div class="flex flex-col gap-2">
                <label for="project-priority" class="text-sm font-medium">Статус</label>
                <Select
                    v-model="form.status"
                    label-id="project-priority"
                    :options="STATUS_OPTIONS"
                    option-label="label"
                    option-value="value"
                    fluid
                />
            </div>

            <div class="flex flex-col gap-2">
                <label for="project-budget" class="text-sm font-medium">Бюджет</label>
                <InputNumber input-id="project-budget" v-model="form.budget" :min="0" mode="currency" currency="USD" locale="en-US" :max-fraction-digits="0" fluid />
            </div>

            <div class="mt-2 flex justify-end gap-2">
                <Button type="button" label="Скасувати" severity="secondary" text @click="visible = false" />
                <Button type="submit" :label="project ? 'Зберегти' : 'Створити'" />
            </div>
        </form>
    </Dialog>
</template>
