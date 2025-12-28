<script setup lang="ts">
import { XIcon } from 'lucide-vue-next';
import { defineEmits, defineProps } from 'vue';

const props = defineProps<{
    modelValue: boolean,
    title?: string,
    submitText?: string
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'submit'): void;
}>();

const closeModal = () => {
    emit('update:modelValue', false);
};
</script>

<template>
    <div v-if="props.modelValue" class="fixed inset-0 flex items-center bg-gray-600/85 justify-center p-4 z-50">
        <!-- Modal Content -->
        <div class="border bg-white shadow rounded-lg px-10 py-5 w-full max-w-3xl relative h-auto overflow-auto">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6 border-b py-3 border-gray-300">
                <h2 class="text-lg font-semibold text-gray-800">{{ title }}</h2>
                <div class="text-gray-400 hover:text-gray-600">
                    <XIcon @click="closeModal" class="cursor-pointer text-[#CA2026] bg-[#FCF2F2] w-9 h-9 rounded p-2" />
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="$emit('submit')" class="space-y-4">
                <div>
                    <slot />
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                    <button type="submit"
                        class="bg-[#CA2026] text-sm cursor-pointer hover:bg-red-600 text-white px-6 py-2.5 rounded font-medium">
                        {{ submitText ?? 'Submit' }}
                    </button>
                    <button type="button" @click="closeModal"
                        class="bg-gray-200 text-sm cursor-pointer hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
