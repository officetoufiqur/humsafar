<script setup lang="ts">
import { ref, watch } from 'vue'
import {  TriangleAlertIcon } from 'lucide-vue-next'

// Define props
const props = defineProps<{
  error?: string
}>()

const visible = ref(false)
const msg = ref('')

// Show error if available
watch(
  () => props.error,
  (newVal) => {
    if (newVal) {
      msg.value = newVal
      visible.value = true
      setTimeout(() => {
        visible.value = false
      }, 5000)
    }
  },
  { immediate: true }
)
</script>
<template>
  <div
    v-if="visible"
    class="flex fixed top-5 right-10 items-center w-full max-w-xs p-4 mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
  >
    <TriangleAlertIcon class="w-6 h-6" />
    <div class="ms-3 text-sm font-normal">{{ error }}</div>
  </div>
</template>


