<script setup lang="ts">
import UserAvatar from '@/shared/ui/UserAvatar.vue';
import { getRoleLabel, getRoleSeverity } from '../model/types';

interface Props {
    name: string;
    email: string;
    role: string;
    showDeleteAction?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    showDeleteAction: true,
})
defineEmits<{onDelete: []}>()
</script>

<template>
    <div class="flex items-center justify-between py-2.5">
        <div class="flex gap-2 items-center">
            <UserAvatar :name="name" />
            <div class="flex flex-col">
                <span class="font-medium text-surface-900 dark:text-surface-0">
                {{ name }}
                </span>
                <span class="text-xs text-surface-500">{{ email }}</span>
            </div>
        </div>
        

        <div class="flex items-center gap-2">
            <Tag
                :value="getRoleLabel(role)"
                :severity="getRoleSeverity(role)"
            />
            <Button
                v-if="props.showDeleteAction"
                icon="pi pi-trash"
                severity="danger"
                text
                rounded
                size="small"
                aria-label="Видалити"
                @click="$emit('onDelete')"
            />
        </div>
    </div>
</template>