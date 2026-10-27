<script setup lang="ts">
  import { MessageCard, MessageSpiner, STATUSES, type AiChatMessage } from '@/entities/ai-chat-message';
  import EmptyList from '@/shared/ui/EmptyList.vue';
  import { useForm, usePage, usePoll } from '@inertiajs/vue3';
  import { ref, nextTick, type VNodeRef, computed, watch } from 'vue';
  
  const isChatOpen = ref(false);
  const chatContainer = ref<VNodeRef | null>(null);

  const page = usePage();
  
  const messages = computed(() => page.props.ai_agent.messages.data);
  const getPeddingMessage = computed<AiChatMessage | undefined>(
    () => messages.value.find((m) => m.status === STATUSES.PENDING)
  );

  const { start, stop } = usePoll(2000, {}, {
    autoStart: false
  })

  const scrollToBottom = async () => {
    await nextTick();

    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  };

  watch(
    getPeddingMessage,
    (hasProcessing) => {
      if (hasProcessing) {
        start()
      } else {
        stop();
      }
      scrollToBottom();
    },
    { immediate: true }
  );
  
  const toggleChat = () => {
    isChatOpen.value = !isChatOpen.value;

    if (isChatOpen.value) {
      scrollToBottom();
    }
  };


  const form = useForm({ message: '' });
  
  const sendMessage = async () => {
    await scrollToBottom();

    form.post('/ai-agents/messages', {
        onStart: async () => {
          await scrollToBottom();
        },
        onSuccess: async () => {
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
              <MessageSpiner v-if="getPeddingMessage?.id === msg.id" />
              
              <MessageCard
                v-else
                :role="msg.role"
                :status="msg.status"
                :content="msg.content"
              />
            </div>

            <EmptyList
              v-if="!messages.length"
              :showAction="false"
              decription="Поки що ви не маэте переписки..."
            />
        </div>
        <template #footer>
            <div class="w-full flex items-center gap-2">
                <Textarea
                    v-model="form.message"
                    placeholder="Напишіть задачу або запит..."
                    class="flex-1 !text-sm"
                    :disabled="form.processing || !!getPeddingMessage"
                    @keyup.enter="sendMessage"
                />
                <Button
                    icon="pi pi-send"
                    size="small"
                    :disabled="!form.message.trim() || form.processing || !!getPeddingMessage"
                    @click="sendMessage"
                />
            </div>
        </template>
    </Dialog>
</template>