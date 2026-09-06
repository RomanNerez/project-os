<script setup lang="ts">
import { computed } from 'vue'
import type { DatePickerProps } from 'primevue/datepicker'
import type { MessageProps } from 'primevue/message'

interface Props {
  label?: string
  labelFor?: string
  
  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']

  modelValue?: DatePickerProps['modelValue']
  name?: string
  placeholder?: string
  fluid?: boolean
  disabled?: boolean
  invalid?: boolean
  size?: DatePickerProps['size']
  mask?: string;
  inputProps?: DatePickerProps
}

const props = withDefaults(defineProps<Props>(), {
  fluid: true,
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: DatePickerProps['modelValue']): void
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

        <DatePicker
            :id="labelFor || name"
            v-model="value"
            :name="name"
            :placeholder="placeholder"
            :fluid="fluid"
            :disabled="disabled"
            :invalid="invalid || !!message"
            :size="size"
            v-bind="inputProps"
            v-mask="mask"
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