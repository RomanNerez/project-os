<script setup lang="ts">
import { computed, ref } from 'vue'
import Eye from '@primeicons/vue/eye';
import EyeSlash from '@primeicons/vue/eye-slash';
import InputPassword, {type InputPasswordProps} from 'primevue/inputpassword'
import type { MessageProps } from 'primevue/message'

interface Props {
  label?: string
  labelFor?: string
  
  message?: string
  messageSeverity?: MessageProps['severity']
  messageVariant?: MessageProps['variant']
  messageSize?: MessageProps['size']

  modelValue?: InputPasswordProps['modelValue']
  name?: string
  placeholder?: string
  fluid?: boolean
  disabled?: boolean
  invalid?: boolean
  size?: InputPasswordProps['size']
  inputProps?: InputPasswordProps
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  fluid: true,
  messageSeverity: 'error',
  messageVariant: 'simple',
  messageSize: 'small'
})

const mask = ref(true);

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
        <IconField>
          <InputPassword
            :id="labelFor || name"
            v-model="value"
            :name="name"
            :placeholder="placeholder"
            :fluid="fluid"
            :disabled="disabled"
            :invalid="invalid || !!message"
            :size="size"
            :mask="mask"
            v-bind="inputProps"
          />

          <InputIcon class="cursor-pointer" @click="mask = !mask">
            <Eye v-if="mask" :size="16" />
            <EyeSlash v-else :size="16" />
          </InputIcon>
        </IconField>
        

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