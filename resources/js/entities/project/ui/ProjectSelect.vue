<script setup lang="ts">
import { onMounted } from 'vue';
import { FormSelect } from '@/shared/ui';
import { useProjectOptions } from '../model/useProjectOptions';
import type { MemberRole, ProjectID } from '../model/types';

const props = withDefaults(defineProps<{
    /** Hide projects where the current user is a member with one of these roles. */
    excludeRoles?: MemberRole[];
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

onMounted(() => load());
</script>

<template>
    <FormSelect
        v-model="model"
        :options="projects"
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
