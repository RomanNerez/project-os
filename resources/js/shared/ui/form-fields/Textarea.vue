<script setup lang="ts">
import { computed } from 'vue'
import PrimeTextarea, { type TextareaProps } from 'primevue/textarea'
import type { MessageProps } from 'primevue/message'

interface Props {
  label?: string
  labelFor?: string

  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']

  modelValue?: TextareaProps['modelValue']
  name?: string
  placeholder?: string
  rows?: number
  autoResize?: boolean
  fluid?: boolean
  disabled?: boolean
  invalid?: boolean
  size?: TextareaProps['size']
  textareaProps?: Omit<TextareaProps, 'rows' | 'disabled'>
}

const props = withDefaults(defineProps<Props>(), {
  rows: 3,
  autoResize: false,
  fluid: true,
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val ?? '')
})
</script>

<template>
    <div class="flex flex-col gap-1">
        <Label 
            v-if="label" 
            :for="labelFor || name"
        >
            {{ label }}
        </Label>

        <PrimeTextarea
            :id="labelFor || name"
            v-model="value"
            :name="name"
            :placeholder="placeholder"
            :rows="rows"
            :auto-resize="autoResize"
            :fluid="fluid"
            :disabled="disabled"
            :invalid="invalid || !!message"
            :size="size"
            v-bind="textareaProps"
        />

        <Message 
            v-if="message" 
            :severity="messageSeverity" 
            :size="messageSize" 
            :variant="messageVariant"
        >
        {{ message }}
        </Message>
    </div>
</template>
