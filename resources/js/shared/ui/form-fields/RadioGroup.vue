<script setup lang="ts" generic="T extends string | number | boolean">
import { computed } from 'vue'
import RadioButton from 'primevue/radiobutton'
import Message, { type MessageProps } from 'primevue/message'

export interface RadioOption<TValue = T> {
  label: string
  value: TValue
  disabled?: boolean
  id?: string
}

interface Props {
  label?: string
  name: string
  options: RadioOption<T>[]
  modelValue?: T
  direction?: 'row' | 'col'
  gap?: string
  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']
  disabled?: boolean
  invalid?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  direction: 'row',
  gap: 'gap-4',
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: T): void
}>()

const value = computed({
  get: () => props.modelValue as T,
  set: (val) => emit('update:modelValue', val)
})

const getOptionId = (option: RadioOption<T>, index: number) => {
  return option.id || `${props.name}-${option.value}-${index}`
}
</script>

<template>
  <div class="flex flex-col gap-1">
    <Label v-if="label">
        {{ label }}
    </Label>

    <div 
      class="flex flex-wrap" 
      :class="[
        direction === 'row' ? 'flex-row items-center' : 'flex-col items-start',
        gap
      ]"
    >
      <div 
        v-for="(option, index) in options" 
        :key="getOptionId(option, index)"
        class="flex items-center gap-2"
      >
        <RadioButton
          :inputId="getOptionId(option, index)"
          v-model="value"
          :name="name"
          :value="option.value"
          :disabled="disabled || option.disabled"
          :invalid="invalid || !!message"
        />
        <Label
            :for="getOptionId(option, index)"
            :class="{ 'opacity-60 cursor-not-allowed': disabled || option.disabled }"
        >
            {{ option.label }}
        </Label>
      </div>
    </div>

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