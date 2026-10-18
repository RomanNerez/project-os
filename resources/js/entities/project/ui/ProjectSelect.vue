<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { FormSelect } from '@/shared/ui';
import { useProjectOptions } from '../model/useProjectOptions';
import type { MemberRole, ProjectID } from '../model/types';

const props = withDefaults(defineProps<{
    /** Hide projects where the current user is a member with one of these roles. */
    excludeRoles?: MemberRole[];
    /** The current value's project, kept selectable even when the loaded options don't include it. */
    selected?: { id: ProjectID; title: string } | null;
    label?: string;
    placeholder?: string;
    message?: string;
    showClear?: boolean;
    disabled?: boolean;
    fluid?: boolean;
}>(), {
    excludeRoles: () => [],
    placeholder: 'Оберіть проєкт',
    fluid: true,
});

const model = defineModel<ProjectID | null>({ default: null });

const { projects, loading, load, search } = useProjectOptions(() => props.excludeRoles);

const options = computed(() => {
    const current = props.selected;

    return current && !projects.value.some((project) => project.id === current.id)
        ? [current, ...projects.value]
        : projects.value;
});

onMounted(() => load());
</script>

<template>
    <FormSelect
        v-model="model"
        :options="options"
        option-label="title"
        option-value="id"
        filter
        :loading="loading"
        :label="props.label"
        :placeholder="props.placeholder"
        :message="props.message"
        :show-clear="props.showClear"
        :disabled="props.disabled"
        :fluid="props.fluid"
        @filter="search($event.value)"
    />
</template>
