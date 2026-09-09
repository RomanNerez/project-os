<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { formatDateLong } from '@/shared/lib';
import { projectRoutes } from '../api/projectRoutes';
import { STATUS_META, type ProjectID, type ProjectStatus } from '../model/types';
import { UserAvatar } from '@/shared/ui';

export interface ProjectUser {
  id: number;
  name: string;
  avatar?: string | null;
}

const props = withDefaults(defineProps<{
    id: ProjectID
    title: string;
    description: string;
    activeUntil: string | null;
    proejctStatus: ProjectStatus;
    budget: string | number;
    userName: string;
    members: ProjectUser[];
    progress?: number;
    maxDisplayedMembers?: number;
}>(), {
    maxDisplayedMembers: 3,
    progress: 0,
});

defineEmits<{
    edit: [];
    delete: [];
    manageMembers: []
}>();

const initials = computed(() =>
    props.title
        .split(' ')
        .filter(Boolean)
        .map((part) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase(),
);

const activeUntilLabel = computed(() => formatDateLong(props.activeUntil));

const budgetLabel = computed(() =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0
    }).format(Number(props.budget)),
);

const status = computed(() => STATUS_META[props.proejctStatus]);

const limit = props.maxDisplayedMembers ?? 3;
const visibleMembers = computed(() => props.members?.slice(0, limit) ?? []);
const hiddenMembersCount = computed(() => Math.max(0, (props.members?.length ?? 0) - limit));
</script>

<template>
    <Card>
        <template #content>
            <div class="flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <Avatar :label="initials" size="large" shape="circle" class="shrink-0" />
                    <div class="min-w-0 flex-1">
                        <Link :href="projectRoutes.show(id)" class="block truncate font-semibold hover:underline">
                            {{ title }}
                        </Link>
                        <p v-if="activeUntilLabel" class="text-xs text-muted-color">Активний до {{ activeUntilLabel }}</p>
                    </div>
                    <Tag :value="status.label" :severity="status.severity" />
                </div>

                <p class="line-clamp-2 min-h-10 text-sm text-muted-color">{{ description }}</p>

                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted-color">Прогрес</span>
                        <span class="font-medium">{{progress}}%</span>
                    </div>
                    <ProgressBar :value="progress" :show-value="false" class="h-2!" />
                </div>

                <div class="flex items-center justify-between gap-2 pt-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <UserAvatar :name="userName"/>
                        <div class="flex flex-col min-w-0">
                            <span class="text-[10px] uppercase font-bold text-muted-color tracking-wider leading-none">Власник</span>
                            <span class="text-xs font-medium truncate">{{ userName }}</span>
                        </div>
                    </div>
                    <div v-if="members && members.length > 0" class="flex items-center gap-1.5 shrink-0">
                        <AvatarGroup>
                            <UserAvatar
                                v-for="member in visibleMembers"
                                :key="member.id"
                                :name="member.name"
                                v-tooltip.top="member.name"
                            />
                            <UserAvatar
                                v-if="hiddenMembersCount > 0"
                                :label="`+${hiddenMembersCount}`"
                                v-tooltip.top="`Ще ${hiddenMembersCount} учасників`"
                            />
                        </AvatarGroup>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-[var(--p-surface-200)] pt-3 dark:border-[var(--p-surface-700)]">
                    <div class="text-sm">
                        <span class="text-muted-color">Бюджет: </span>
                        <span class="font-semibold">{{ budgetLabel }}</span>
                    </div>
                    <div class="flex gap-1">
                        <Button icon="pi pi-user" severity="secondary" text size="small" aria-label="Редагувати" @click="$emit('manageMembers')" />
                        <Button icon="pi pi-pencil" severity="secondary" text size="small" aria-label="Редагувати" @click="$emit('edit')" />
                        <Button icon="pi pi-trash" severity="danger" text size="small" aria-label="Видалити" @click="$emit('delete')" />
                    </div>
                </div>
            </div>
        </template>
    </Card>
</template>
