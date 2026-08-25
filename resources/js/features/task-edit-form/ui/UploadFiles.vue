<script setup>
  import { ref, computed } from 'vue';
  import Plus from '@primeicons/vue/plus';
  import Times from '@primeicons/vue/times';
  import Upload from '@primeicons/vue/upload';
  
  const fu = ref(null);
  
  // 1. Початковий список вже завантажених файлів (наприклад, отримані з бази даних/сервера)
  const uploadedFiles = ref([
    {
      id: 101,
      name: 'existing-image-1.jpg',
      size: 245000,
      url: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1.jpg',
      isUploaded: true
    }
  ]);
  
  // 2. Список локальних файлів, обраних для завантаження
  const pendingFiles = ref([]);
  
  // 3. Загальний об'єднаний список
  const allMedia = computed(() => {
    const pendingFormatted = pendingFiles.value.map(file => ({
      file: file, // оригінальний File об'єкт
      name: file.name,
      size: file.size,
      url: file.objectURL,
      isUploaded: false
    }));
  
    return [...uploadedFiles.value, ...pendingFormatted];
  });
  
  // Кількість файлів, що чекають на відправку
  const pendingFilesCount = computed(() => pendingFiles.value.length);
  
  // Подія при виборі файлів
  const onSelect = (event) => {
    pendingFiles.value = event.files;
  };
  
  // Подія після успішного завантаження на сервер
  const onUpload = (event) => {
    // Припустимо, backend повертає масив завантажених об'єктів
    // Переносимо їх у масив вже завантажених
    pendingFiles.value.forEach(file => {
      uploadedFiles.value.push({
        id: Date.now() + Math.random(),
        name: file.name,
        size: file.size,
        url: file.objectURL,
        isUploaded: true
      });
    });
  
    // Очищаємо список нових файлів у PrimeVue
    pendingFiles.value = [];
    fu.value.clear();
  };
  
  // Видалення файлу із загального списку
  const removeItem = (item) => {
    if (item.isUploaded) {
      // Видаляємо вже завантажений файл (тут можна додати API запит DELETE на сервер)
      uploadedFiles.value = uploadedFiles.value.filter(f => f.id !== item.id);
    } else {
      // Видаляємо файл, який ще не завантажився, з компонента PrimeVue
      const indexInPending = pendingFiles.value.findIndex(f => f === item.file);
      if (indexInPending !== -1) {
        fu.value.remove(null, indexInPending);
        pendingFiles.value.splice(indexInPending, 1);
      }
    }
  };
  
  const formatSize = (bytes) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
  };
</script>

<template>
    <FileUpload
      ref="fu"
      name="demo[]"
      url="/api/upload"
      :multiple="true"
      accept="image/*"
      :maxFileSize="1000000"
      mode="advanced"
      :pt="{
        root: { class: '!border-0' },
        header: { class: '!p-0' },
        content: { class: '!p-0' }
      }"
      @select="onSelect"
      @upload="onUpload"
    >
      <template #header="{ uploadCallback }">
        <div class="flex items-center gap-2">
          <Button type="button" severity="secondary" variant="outlined" @click="$refs.fu.choose()">
            <Plus />
            Add Files
          </Button>
          <Button v-if="pendingFilesCount > 0" type="button" @click="uploadCallback">
            <Upload />
            Upload New ({{ pendingFilesCount }})
          </Button>
        </div>
      </template>
  
      <!-- Загальний вміст (Content) -->
      <template #content>
        <!-- Загальний список: завантажені + нові -->
        <div v-if="allMedia.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
          <div 
            v-for="(item, i) of allMedia" 
            :key="item.id || item.file?.name" 
            class="group relative rounded-lg overflow-hidden border border-surface-200 dark:border-surface-700"
          >
            <!-- Зображення -->
            <img :src="item.url" :alt="item.name" class="w-full h-32 object-cover" />
  
            <!-- Статус бейдж -->
            <div class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider text-white"
                 :class="item.isUploaded ? 'bg-green-500' : 'bg-amber-500'">
              {{ item.isUploaded ? 'Uploaded' : 'Pending' }}
            </div>
  
            <!-- Оверлей з кнопкою видалення -->
            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
              <button 
                type="button" 
                class="text-white bg-red-500 rounded-full p-2 hover:bg-red-600 cursor-pointer border-0" 
                @click="removeItem(item, i)"
              >
                <Times />
              </button>
            </div>
  
            <!-- Інфо про файл -->
            <div class="p-2">
              <div class="text-xs font-medium truncate">{{ item.name }}</div>
              <div class="text-xs text-muted-color">{{ formatSize(item.size) }}</div>
            </div>
          </div>
        </div>
      </template>
  
      <!-- Порожній стан -->
      <template #empty>
        <div v-if="allMedia.length === 0" class="border-2 border-dashed border-surface-200 dark:border-surface-700 rounded-xl p-12 text-center">
          <p class="text-muted-color m-0!">No images added or uploaded yet.</p>
        </div>
      </template>
    </FileUpload>
</template>