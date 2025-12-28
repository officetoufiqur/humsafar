<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';

defineProps<{
  forr?: string;
  label?: string;
  type?: string;
  id?: string;
  name?: string;
  placeholder?: string;
  modelValue?: string;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

function updateValue(event: Event) {
  const target = event.target as HTMLInputElement | null;
  if (target) {
    emit('update:modelValue', target.value);
  }
}
</script>

<template>
  <div class="w-full">
    <label :for="forr" class="text-[#5D5D5D] font-medium text-sm">{{ label }}</label>
    <input
      :type="type"
      :id="forr"
      :name="forr"
      :placeholder="placeholder"
      :value="modelValue"
      @input="updateValue"
      class="border border-gray-300 rounded px-3 py-2 w-full mt-1 text-sm"
    />
  </div>
</template>
