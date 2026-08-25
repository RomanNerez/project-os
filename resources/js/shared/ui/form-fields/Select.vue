<script setup lang="ts">
import { computed } from 'vue'
import PrimeSelect, { type SelectProps } from 'primevue/select'
import type { MessageProps } from 'primevue/message'

interface Props {
  label?: string
  labelFor?: string

  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']

  modelValue?: SelectProps['modelValue']
  name?: string
  options?: SelectProps['options']
  optionLabel?: SelectProps['optionLabel']
  optionValue?: SelectProps['optionValue']
  optionDisabled?: SelectProps['optionDisabled']
  optionGroupLabel?: SelectProps['optionGroupLabel']
  optionGroupChildren?: SelectProps['optionGroupChildren']
  placeholder?: string
  showClear?: boolean
  filter?: boolean
  filterPlaceholder?: string
  loading?: boolean
  fluid?: boolean
  disabled?: boolean
  invalid?: boolean
  size?: SelectProps['size']
  selectProps?: SelectProps
}

const props = withDefaults(defineProps<Props>(), {
  fluid: true,
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: SelectProps['modelValue']): void
}>()

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
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

        <PrimeSelect
            :id="labelFor || name"
            v-model="value"
            :name="name"
            :options="options"
            :option-label="optionLabel"
            :option-value="optionValue"
            :option-disabled="optionDisabled"
            :option-group-label="optionGroupLabel"
            :option-group-children="optionGroupChildren"
            :placeholder="placeholder"
            :show-clear="showClear"
            :filter="filter"
            :filter-placeholder="filterPlaceholder"
            :loading="loading"
            :fluid="fluid"
            :disabled="disabled"
            :invalid="invalid || !!message"
            :size="size"
            v-bind="selectProps"
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
