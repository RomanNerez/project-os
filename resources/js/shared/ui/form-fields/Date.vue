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

const pickerProps = computed(() => ({
  id: props.labelFor || props.name,
  name: props.name,
  placeholder: props.placeholder,
  fluid: props.fluid,
  disabled: props.disabled,
  invalid: props.invalid || !!props.message,
  size: props.size,
  ...props.inputProps,
}))
</script>

<template>
    <div class="flex flex-col gap-1">
        <Label 
            v-if="label" 
            :for="labelFor || name"
        >
            {{ label }}
        </Label>

        <!-- PrimeVue's v-mask with an empty mask clears the input on blur and nulls the model, so it is only applied when a mask is given. -->
        <DatePicker v-if="mask" v-model="value" v-bind="pickerProps" v-mask="mask" />
        <DatePicker v-else v-model="value" v-bind="pickerProps" />

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