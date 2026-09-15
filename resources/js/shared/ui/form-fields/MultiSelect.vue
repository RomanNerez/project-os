<script setup lang="ts">
import { computed } from 'vue'
import PrimeMultiSelect, { type MultiSelectProps } from 'primevue/multiselect'
import type { MessageProps } from 'primevue/message'

interface Props {
  label?: string
  labelFor?: string

  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']

  modelValue?: MultiSelectProps['modelValue']
  name?: string
  options?: MultiSelectProps['options']
  optionLabel?: MultiSelectProps['optionLabel']
  optionValue?: MultiSelectProps['optionValue']
  optionDisabled?: MultiSelectProps['optionDisabled']
  optionGroupLabel?: MultiSelectProps['optionGroupLabel']
  optionGroupChildren?: MultiSelectProps['optionGroupChildren']
  placeholder?: string
  showClear?: boolean
  filter?: boolean
  filterPlaceholder?: string
  display?: MultiSelectProps['display']
  maxSelectedLabels?: number
  selectedItemsLabel?: string
  showToggleAll?: boolean
  loading?: boolean
  fluid?: boolean
  disabled?: boolean
  invalid?: boolean
  size?: MultiSelectProps['size']
  multiSelectProps?: MultiSelectProps
}

const props = withDefaults(defineProps<Props>(), {
  fluid: true,
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: MultiSelectProps['modelValue']): void
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

        <PrimeMultiSelect
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
            :display="display"
            :max-selected-labels="maxSelectedLabels"
            :selected-items-label="selectedItemsLabel"
            :show-toggle-all="showToggleAll"
            :loading="loading"
            :fluid="fluid"
            :disabled="disabled"
            :invalid="invalid || !!message"
            :size="size"
            v-bind="multiSelectProps"
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
