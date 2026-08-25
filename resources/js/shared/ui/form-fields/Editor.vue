<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { EditorContent, useEditor } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import TextAlign from '@tiptap/extension-text-align'
import type { MessageProps } from 'primevue/message'

type HeadingLevel = 1 | 2 | 3 | 4 | 5 | 6

interface ToolbarItem {
  icon: string
  title: string
  isActive: () => boolean
  run: () => void
}

interface Props {
  label?: string
  labelFor?: string

  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']

  modelValue?: string
  name?: string
  placeholder?: string
  disabled?: boolean
  invalid?: boolean
  headingLevels?: HeadingLevel[]
  minHeight?: string
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  placeholder: '',
  headingLevels: () => [1, 2, 3, 4],
  minHeight: '180px',
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const EMPTY_HTML = ''

const editor = useEditor({
  content: props.modelValue,
  editable: !props.disabled,

  extensions: [
    StarterKit.configure({
      heading: { levels: props.headingLevels },
      codeBlock: false,
      link: {
        openOnClick: false,
        autolink: true,
        defaultProtocol: 'https',
        HTMLAttributes: {
          class: 'text-primary underline cursor-pointer',
          rel: 'noopener noreferrer nofollow',
          target: '_blank'
        }
      }
    }),
    Placeholder.configure({
      placeholder: () => props.placeholder
    }),
    TextAlign.configure({
      types: ['heading', 'paragraph']
    })
  ],

  editorProps: {
    attributes: {
      class: 'px-3 py-2 outline-none'
    }
  },

  onUpdate: ({ editor }) => {
    const html = editor.getHTML()
    emit('update:modelValue', html === EMPTY_HTML ? '' : html)
  }
})

watch(
  () => props.modelValue,
  (value) => {
    if (!editor.value || editor.value.getHTML() === (value || EMPTY_HTML)) return

    editor.value.commands.setContent(value || '', { emitUpdate: false })
  }
)

watch(
  () => props.disabled,
  (disabled) => editor.value?.setEditable(!disabled)
)

const blockOptions = computed(() => [
  { label: 'Звичайний текст', value: 0 },
  ...props.headingLevels.map((level) => ({ label: `Заголовок ${level}`, value: level }))
])

const activeBlock = computed({
  get: () => props.headingLevels.find((level) => editor.value?.isActive('heading', { level })) ?? 0,
  set: (value: number) => {
    const chain = editor.value?.chain().focus()

    if (!chain) return

    if (value === 0) {
      chain.setParagraph().run()
      return
    }

    chain.setHeading({ level: value as HeadingLevel }).run()
  }
})

const toolbarGroups = computed<ToolbarItem[][]>(() => {
  const chain = () => editor.value!.chain().focus()
  const isActive = (name: string) => () => !!editor.value?.isActive(name)
  const isAligned = (align: string) => () => !!editor.value?.isActive({ textAlign: align })

  return [
    [
      { icon: 'pi pi-bold', title: 'Жирний', isActive: isActive('bold'), run: () => chain().toggleBold().run() },
      { icon: 'pi pi-italic', title: 'Курсив', isActive: isActive('italic'), run: () => chain().toggleItalic().run() },
      { icon: 'pi pi-underline', title: 'Підкреслений', isActive: isActive('underline'), run: () => chain().toggleUnderline().run() },
      { icon: 'pi pi-strikethrough', title: 'Закреслений', isActive: isActive('strike'), run: () => chain().toggleStrike().run() }
    ],
    [
      { icon: 'pi pi-list', title: 'Маркований список', isActive: isActive('bulletList'), run: () => chain().toggleBulletList().run() },
      { icon: 'pi pi-list-ol', title: 'Нумерований список', isActive: isActive('orderedList'), run: () => chain().toggleOrderedList().run() }
    ],
    [
      { icon: 'pi pi-align-left', title: 'По лівому краю', isActive: isAligned('left'), run: () => chain().setTextAlign('left').run() },
      { icon: 'pi pi-align-center', title: 'По центру', isActive: isAligned('center'), run: () => chain().setTextAlign('center').run() },
      { icon: 'pi pi-align-right', title: 'По правому краю', isActive: isAligned('right'), run: () => chain().setTextAlign('right').run() },
      { icon: 'pi pi-align-justify', title: 'По ширині', isActive: isAligned('justify'), run: () => chain().setTextAlign('justify').run() }
    ]
  ]
})

const linkDialogVisible = ref(false)
const linkUrl = ref('')

function openLinkDialog(): void {
  linkUrl.value = editor.value?.getAttributes('link').href ?? ''
  linkDialogVisible.value = true
}

function withProtocol(url: string): string {
  return /^([a-z]+:|\/|#)/i.test(url) ? url : `https://${url}`
}

function applyLink(): void {
  const url = linkUrl.value.trim()
  const chain = editor.value?.chain().focus().extendMarkRange('link')

  if (!chain) return

  if (url) {
    chain.setLink({ href: withProtocol(url) }).run()
  } else {
    chain.unsetLink().run()
  }

  linkDialogVisible.value = false
}

function removeLink(): void {
  editor.value?.chain().focus().extendMarkRange('link').unsetLink().run()
}

defineExpose({ editor })
</script>

<template>
    <div class="flex flex-col gap-1">
        <Label
            v-if="label"
            :for="labelFor || name"
        >
            {{ label }}
        </Label>

        <div
            class="editor overflow-hidden rounded-md border"
            :class="invalid || message ? 'border-red-500' : 'border-gray-300'"
        >
            <div
                v-if="editor"
                class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 p-1"
            >
                <Select
                    v-model="activeBlock"
                    :options="blockOptions"
                    option-label="label"
                    option-value="value"
                    :disabled="disabled"
                    size="small"
                    class="w-44"
                />

                <template v-for="(group, index) in toolbarGroups" :key="index">
                    <div class="mx-1 h-6 w-px bg-gray-300"></div>

                    <Button
                        v-for="item in group"
                        :key="item.icon"
                        type="button"
                        text
                        size="small"
                        :icon="item.icon"
                        :severity="item.isActive() ? 'primary' : 'secondary'"
                        :class="{ '!bg-gray-200': item.isActive() }"
                        :aria-label="item.title"
                        :title="item.title"
                        :disabled="disabled"
                        @click="item.run()"
                    />
                </template>

                <div class="mx-1 h-6 w-px bg-gray-300"></div>

                <Button
                    type="button"
                    text
                    size="small"
                    icon="pi pi-link"
                    :severity="editor.isActive('link') ? 'primary' : 'secondary'"
                    :class="{ '!bg-gray-200': editor.isActive('link') }"
                    aria-label="Посилання"
                    title="Посилання"
                    :disabled="disabled"
                    @click="openLinkDialog"
                />

                <Button
                    type="button"
                    text
                    size="small"
                    icon="pi pi-eraser"
                    severity="secondary"
                    aria-label="Видалити посилання"
                    title="Видалити посилання"
                    :disabled="disabled || !editor.isActive('link')"
                    @click="removeLink"
                />
            </div>

            <EditorContent :editor="editor" class="editor-content" />
        </div>

        <Message
            v-if="message"
            :severity="messageSeverity"
            :size="messageSize"
            :variant="messageVariant"
        >
            {{ message }}
        </Message>

        <Dialog
            v-model:visible="linkDialogVisible"
            modal
            header="Посилання"
            class="w-full max-w-md"
        >
            <InputText
                v-model="linkUrl"
                autofocus
                fluid
                placeholder="https://example.com"
                @keydown.enter.prevent="applyLink"
            />

            <template #footer>
                <Button type="button" label="Скасувати" severity="secondary" text @click="linkDialogVisible = false" />
                <Button type="button" label="Зберегти" @click="applyLink" />
            </template>
        </Dialog>
    </div>
</template>

<style scoped>
.editor-content :deep(.tiptap) {
    min-height: v-bind(minHeight);
    overflow-wrap: anywhere;
}

.editor-content :deep(.tiptap p.is-editor-empty:first-child::before) {
    content: attr(data-placeholder);
    color: var(--p-text-muted-color, #6b7280);
    float: left;
    height: 0;
    pointer-events: none;
}

.editor-content :deep(.tiptap h1) {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0.6em 0 0.3em;
}

.editor-content :deep(.tiptap h2) {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0.6em 0 0.3em;
}

.editor-content :deep(.tiptap h3) {
    font-size: 1.15rem;
    font-weight: 600;
    margin: 0.6em 0 0.3em;
}

.editor-content :deep(.tiptap h4),
.editor-content :deep(.tiptap h5),
.editor-content :deep(.tiptap h6) {
    font-size: 1rem;
    font-weight: 600;
    margin: 0.6em 0 0.3em;
}

.editor-content :deep(.tiptap p) {
    margin: 0.25em 0;
}

.editor-content :deep(.tiptap ul),
.editor-content :deep(.tiptap ol) {
    margin: 0.25em 0;
    padding-left: 1.5rem;
}

.editor-content :deep(.tiptap ul) {
    list-style: disc;
}

.editor-content :deep(.tiptap ol) {
    list-style: decimal;
}

.editor-content :deep(.tiptap blockquote) {
    border-left: 3px solid var(--p-surface-300, #d1d5db);
    margin: 0.5em 0;
    padding-left: 0.75rem;
    color: var(--p-text-muted-color, #6b7280);
}
</style>
