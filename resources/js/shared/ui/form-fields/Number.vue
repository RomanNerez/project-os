<script setup lang="ts">
import { computed } from 'vue'
import type { InputNumberProps } from 'primevue/inputnumber'
import type { MessageProps } from 'primevue/message'

interface Props {
  label?: string
  labelFor?: string

  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']

  modelValue?: number | null
  name?: string
  placeholder?: string
  min?: number
  max?: number
  minFractionDigits?: number
  maxFractionDigits?: number
  locale?: string
  mode?: InputNumberProps['mode']
  currency?: string
  fluid?: boolean
  disabled?: boolean
  invalid?: boolean
  size?: InputNumberProps['size']
  inputProps?: InputNumberProps
}

const props = withDefaults(defineProps<Props>(), {
  fluid: true,
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void
}>()

const value = computed({
  get: () => props.modelValue ?? null,
  set: (val) => emit('update:modelValue', val ?? null)
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

        <InputNumber
            v-model="value"
            :input-id="labelFor || name"
            :name="name"
            :placeholder="placeholder"
            :min="min"
            :max="max"
            :min-fraction-digits="minFractionDigits"
            :max-fraction-digits="maxFractionDigits"
            :locale="locale"
            :mode="mode"
            :currency="currency"
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
