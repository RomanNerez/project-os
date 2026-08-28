<script setup lang="ts">
    import { UserAvatar } from '@/entities/user';

    interface Props {
        userName: string;
        createdAt: string;
        body: string;
        showAction?: boolean;
    }

    const props = withDefaults(defineProps<Props>(), {
        showAction: true,
    });

    defineEmits(['onEdit', 'onDelete'])
</script>

<template>
    <div class="flex items-start gap-4">
        <UserAvatar
            :name="userName"
            shape="circle"
            size="medium" 
            class="flex-shrink-0"
        />

        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                <span class="font-semibold text-gray-900 text-sm">{{ userName }}</span>
                <span class="text-xs text-gray-400">• {{ createdAt }}</span>
                </div>

                <div v-if="showAction" class="flex items-center gap-1">
                    <Button 
                        icon="pi pi-pencil" 
                        text 
                        rounded 
                        severity="secondary" 
                        size="small"
                        aria-label="Редагувати"
                        @click="$emit('onEdit')"
                    />
                    <Button 
                        icon="pi pi-trash" 
                        text 
                        rounded 
                        severity="danger" 
                        size="small"
                        aria-label="Видалити"
                        @click="$emit('onDelete')"
                    />
                </div>
            </div>

            <div  class="text-gray-700 text-sm whitespace-pre-line leading-relaxed">
                {{ body }}
            </div>
        </div>
    </div>
</template>
