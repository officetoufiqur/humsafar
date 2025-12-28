<script setup lang="ts">
import { ref, watch } from 'vue'
import { BadgeCheckIcon } from 'lucide-vue-next'

// Define props
const props = defineProps<{
  message?: string
}>()

const visible = ref(false)
const msg = ref('')

// Show message if available
watch(
  () => props.message,
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
    class="flex fixed top-5 right-10 items-center w-full max-w-xs p-4 mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
  >
    <BadgeCheckIcon class="w-6 h-6" />
    <div class="ms-3 text-sm font-normal">{{ message }}</div>
  </div>
</template>


