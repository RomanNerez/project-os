<script setup lang="ts">
import { computed, reactive, watch } from 'vue';
import { PRIORITY_OPTIONS, type Project, type ProjectDraft } from '@/entities/project';

const props = defineProps<{
    /** Проєкт для редагування; null — створення нового */
    project: Project | null;
}>();

const emit = defineEmits<{
    submit: [draft: ProjectDraft];
}>();

const visible = defineModel<boolean>('visible', { required: true });

const form = reactive<ProjectDraft>(emptyDraft());

function emptyDraft(): ProjectDraft {
    return {
        name: '',
        description: '',
        activeUntil: new Date(),
        priority: 'medium',
        progress: 0,
        budget: 0,
    };
}

watch(visible, (isOpen) => {
    if (isOpen) {
        Object.assign(form, props.project ? { ...props.project, activeUntil: new Date(props.project.activeUntil) } : emptyDraft());
    }
});

const isValid = computed(() => form.name.trim().length > 0);

function submit(): void {
    if (!isValid.value) return;
    emit('submit', { ...form, name: form.name.trim() });
    visible.value = false;
}
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
                <InputText id="project-name" v-model="form.name" placeholder="Назва проєкту" autofocus fluid />
            </div>

            <div class="flex flex-col gap-2">
                <label for="project-description" class="text-sm font-medium">Опис</label>
                <Textarea id="project-description" v-model="form.description" rows="3" placeholder="Короткий опис проєкту" auto-resize fluid />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label for="project-active-until" class="text-sm font-medium">Активний до</label>
                    <DatePicker input-id="project-active-until" v-model="form.activeUntil" date-format="dd.mm.yy" show-icon fluid />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="project-priority" class="text-sm font-medium">Пріоритет</label>
                    <Select
                        v-model="form.priority"
                        label-id="project-priority"
                        :options="PRIORITY_OPTIONS"
                        option-label="label"
                        option-value="value"
                        fluid
                    />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label for="project-progress" class="text-sm font-medium">Прогрес, %</label>
                    <InputNumber input-id="project-progress" v-model="form.progress" :min="0" :max="100" suffix="%" fluid />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="project-budget" class="text-sm font-medium">Бюджет</label>
                    <InputNumber input-id="project-budget" v-model="form.budget" :min="0" mode="currency" currency="USD" locale="en-US" :max-fraction-digits="0" fluid />
                </div>
            </div>

            <div class="mt-2 flex justify-end gap-2">
                <Button type="button" label="Скасувати" severity="secondary" text @click="visible = false" />
                <Button type="submit" :label="project ? 'Зберегти' : 'Створити'" :disabled="!isValid" />
            </div>
        </form>
    </Dialog>
</template>
