<script setup lang="ts">
import { computed } from 'vue'
import type { InputTextProps } from 'primevue/inputtext'
import type { MessageProps } from 'primevue/message'

interface Props {
  label?: string
  labelFor?: string
  
  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']

  modelValue?: InputTextProps['modelValue']
  name?: string
  type?: string
  placeholder?: string
  fluid?: boolean
  disabled?: boolean
  invalid?: boolean
  size?: InputTextProps['size']
  inputProps?: InputTextProps
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  fluid: true,
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number): void
}>()

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val!)
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

        <InputText
            :id="labelFor || name"
            v-model="value"
            :name="name"
            :type="type"
            :placeholder="placeholder"
            :fluid="fluid"
            :disabled="disabled"
            :invalid="invalid || !!message"
            :size="size"
            v-bind="inputProps"
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