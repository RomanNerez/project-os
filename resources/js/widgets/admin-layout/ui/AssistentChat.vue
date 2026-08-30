<script setup lang="ts">
  import { useForm, usePage } from '@inertiajs/vue3';
  import { ref, nextTick, type VNodeRef, computed } from 'vue';
  
  const isChatOpen = ref(false);
  const chatContainer = ref<VNodeRef | null>(null);

  const page = usePage();
  
  const messages = computed(() => page.props.ai_agent.messages.data);
  
  const toggleChat = () => {
    isChatOpen.value = !isChatOpen.value;

    if (isChatOpen.value) {
      scrollToBottom();
    }
  };
  
  const scrollToBottom = async () => {
    await nextTick();

    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  };

  const form = useForm({ message: '' });
  
  const sendMessage = async () => {
    await scrollToBottom();

    form.post('/ai-agents/messages', {
        onStart: async () => {
          await scrollToBottom();
        },
        onSuccess: async (response) => {
          form.reset();
          await scrollToBottom();
        },
    })
  };
</script>

<template>
    <Button
        :icon="isChatOpen ? 'pi pi-times' : 'pi pi-comments'"
        rounded
        raised
        class="!fixed bottom-6 right-6 !w-14 !h-14 z-50 shadow-xl transition-transform duration-200 hover:scale-105"
        @click="toggleChat"
    />
    
    <Dialog 
        v-model:visible="isChatOpen" 
        modal 
        :dismissableMask="true"
        position="bottomright"
        header="Чат"
        :pt="{
            root: {
                class: 'w-full sm:w-96 !m-0 sm:!m-[1rem] !max-h-full !h-full sm:!h-[60%]  !rounded-none sm:!rounded-xl overflow-hidden'
            },
            header: {
                class: 'flex items-center justify-between !p-4 border-b border-[var(--p-surface-100)] bg-[var(--p-surface-50)]'
            },
            content: {
                class: '!p-0 !overflow-hidden'
            },
            footer: {
                class: '!p-4 border-t border-[var(--p-surface-100)]'
            }
        }"
      >
        <template #header>
            <div class="flex items-center gap-3">
                <Avatar icon="pi pi-user" shape="circle" class="bg-primary text-primary-contrast" />
                <div>
                    <h3 class="font-semibold text-sm m-0 text-surface-900 dark:text-surface-0">AI Помічник</h3>
                    <p class="text-xs text-surface-500 m-0">Менеджер задач та аналітики</p>
                </div>
            </div>
        </template>
        <div ref="chatContainer" class="h-full flex-1 overflow-y-auto p-4 space-y-3">
            <div
              v-for="(msg, index) in messages"
              :key="index"
              class="flex"
              :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
            >
              <div
                class="max-w-[85%] p-3 text-sm leading-relaxed"
                :class="
                  msg.role === 'user'
                    ? 'bg-[var(--p-primary-color)] text-[var(--p-primary-contrast-color)] rounded-2xl rounded-br-none'
                    : 'bg-[var(--p-surface-100)] dark:bg-[var(--p-surface-800)] text-[var(--p-surface-900)] dark:text-[var(--p-surface-0)] rounded-2xl rounded-bl-none'
                "
              >
                {{ msg.content }}
              </div>
            </div>
  
            <div v-if="form.processing" class="flex justify-start">
              <div class="bg-[var(--p-surface-100)] dark:bg-[var(--p-surface-800)] p-3 rounded-2xl rounded-bl-none flex items-center gap-2">
                <ProgressSpinner style="width: 18px; height: 18px" strokewidth="6" />
                <span class="text-xs text-surface-500">Думаю...</span>
              </div>
            </div>
        </div>
        <template #footer>
            <div class="w-full flex items-center gap-2">
                <Textarea
                    v-model="form.message"
                    placeholder="Напишіть задачу або запит..."
                    class="flex-1 !text-sm"
                    :disabled="form.processing"
                    @keyup.enter="sendMessage"
                />
                <Button
                    icon="pi pi-send"
                    size="small"
                    :disabled="!form.message.trim() || form.processing"
                    @click="sendMessage"
                />
            </div>
        </template>
    </Dialog>
</template>