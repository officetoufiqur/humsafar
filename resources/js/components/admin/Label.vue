<script setup lang="ts">
import { defineProps } from 'vue';

defineProps<{
  forr?: string;
  label?: string;
}>();

</script>

<template>
  <label :for="forr" class="text-[#5D5D5D] font-medium text-sm">{{ label }}</label>
</template>
