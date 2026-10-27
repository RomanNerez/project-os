<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { UserProfile } from '@/entities/user';
import { ProfileInfoForm } from '@/features/profile-info-form';
import { ProfilePasswordForm } from '@/features/profile-password-form';
import { WorkspaceLayout } from '@/widgets/workspace-layout';

defineProps<{
    user: { data: UserProfile };
}>();
</script>

<template>
    <Head title="Профіль" />
    <WorkspaceLayout title="Профіль" description="Особисті дані та безпека облікового запису">
        <div class="rounded-lg border border-[var(--p-content-border-color)] bg-[var(--p-content-background)] shadow-sm">
            <Tabs value="info">
                <TabList>
                    <Tab value="info" class="flex items-center gap-2">
                        <i class="pi pi-user" />
                        <span>Основна інформація</span>
                    </Tab>
                    <Tab value="password" class="flex items-center gap-2">
                        <i class="pi pi-lock" />
                        <span>Пароль</span>
                    </Tab>
                </TabList>
                <TabPanels>
                    <TabPanel value="info">
                        <ProfileInfoForm :user="user.data" />
                    </TabPanel>
                    <TabPanel value="password">
                        <ProfilePasswordForm />
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </WorkspaceLayout>
</template>
