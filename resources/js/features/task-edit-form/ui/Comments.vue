<script setup>
import { ref } from 'vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import Textarea from 'primevue/textarea'
import Avatar from 'primevue/avatar'
import Menu from 'primevue/menu'

const comments = ref([
  {
    id: 1,
    author: 'Олексій Іванов',
    avatar: 'https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png',
    text: 'Чудовий проект! Підкажіть, будь ласка, чи плануєте ви додавати підтримку темної теми?',
    createdAt: '24 Серп, 14:30',
    isEditing: false
  },
  {
    id: 2,
    author: 'Марія Петренко',
    avatar: 'https://primefaces.org/cdn/primevue/images/avatar/asiyakumu.png',
    text: 'Дякую, все працює чудово після останнього оновлення.',
    createdAt: '24 Серп, 15:15',
    isEditing: false
  }
])

const newCommentText = ref('')

const editText = ref('')

const addComment = () => {
  if (!newCommentText.value.trim()) return

  comments.value.unshift({
    id: Date.now(),
    author: 'Поточний Користувач',
    avatar: 'https://primefaces.org/cdn/primevue/images/avatar/onyamalimba.png',
    text: newCommentText.value,
    createdAt: 'Щойно',
    isEditing: false
  })

  newCommentText.value = ''
}

const startEdit = (comment) => {
  comments.value.forEach(c => c.isEditing = false)
  
  comment.isEditing = true
  editText.value = comment.text
}

const cancelEdit = (comment) => {
  comment.isEditing = false
  editText.value = ''
}

const saveEdit = (comment) => {
  if (!editText.value.trim()) return

  comment.text = editText.value
  comment.isEditing = false
  editText.value = ''
}

const deleteComment = (id) => {
  comments.value = comments.value.filter(c => c.id !== id)
}
</script>

<template>
  <div class="max-w-3xl mx-auto">
    <div >
        <div class="flex gap-4">
          <Avatar 
            image="https://primefaces.org/cdn/primevue/images/avatar/onyamalimba.png" 
            shape="circle" 
            size="large" 
          />
          <div class="flex-1 space-y-3">
            <Textarea 
              v-model="newCommentText" 
              rows="3" 
              placeholder="Напишіть ваш коментар..." 
              class="w-full resize-none p-3 border-gray-200 focus:border-primary-500 rounded-lg"
              autoResize
            />
            <div class="flex justify-end">
              <Button 
                label="Надіслати" 
                icon="pi pi-send" 
                :disabled="!newCommentText.trim()"
                @click="addComment" 
                size="small"
              />
            </div>
          </div>
        </div>
    </div>

    <!-- Список коментарів -->
    <div class="space-y-4">
      <div 
        v-for="comment in comments" 
        :key="comment.id"
        class="bg-white rounded-xl p-4 transition-all"
      >
        <div class="flex items-start gap-4">
          <!-- Аватар -->
          <Avatar 
            :image="comment.avatar" 
            shape="circle" 
            size="medium" 
            class="flex-shrink-0"
          />

          <div class="flex-1 min-w-0">
            <!-- Шапка коментаря -->
            <div class="flex items-center justify-between mb-1">
              <div class="flex items-center gap-2">
                <span class="font-semibold text-gray-900 text-sm">{{ comment.author }}</span>
                <span class="text-xs text-gray-400">• {{ comment.createdAt }}</span>
              </div>

              <!-- Кнопки дій (якщо не в режимі редагування) -->
              <div v-if="!comment.isEditing" class="flex items-center gap-1">
                <Button 
                  icon="pi pi-pencil" 
                  text 
                  rounded 
                  severity="secondary" 
                  size="small"
                  aria-label="Редагувати"
                  @click="startEdit(comment)"
                />
                <Button 
                  icon="pi pi-trash" 
                  text 
                  rounded 
                  severity="danger" 
                  size="small"
                  aria-label="Видалити"
                  @click="deleteComment(comment.id)"
                />
              </div>
            </div>

            <!-- Режим перегляду -->
            <div v-if="!comment.isEditing" class="text-gray-700 text-sm whitespace-pre-line leading-relaxed">
              {{ comment.text }}
            </div>

            <!-- Режим редагування (Інлайн) -->
            <div v-else class="space-y-3 mt-2">
              <Textarea 
                v-model="editText" 
                rows="2" 
                class="w-full resize-none p-2 border-gray-300 rounded-md text-sm"
                autoResize
              />
              <div class="flex justify-end gap-2">
                <Button 
                  label="Скасувати" 
                  severity="secondary" 
                  text 
                  size="small" 
                  @click="cancelEdit(comment)" 
                />
                <Button 
                  label="Зберегти" 
                  icon="pi pi-check" 
                  size="small" 
                  :disabled="!editText.trim()"
                  @click="saveEdit(comment)" 
                />
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>