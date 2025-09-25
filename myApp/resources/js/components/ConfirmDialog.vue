<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
  modelValue: boolean;      // control open/close from parent
  title?: string;           // optional title
  message?: string;         // optional message
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'confirm'): void;
}>();

const open = ref(props.modelValue);

watch(() => props.modelValue, (val) => {
  open.value = val;
});

function close() {
  emit('update:modelValue', false);
}

function confirm() {
  emit('confirm');
  close();
}
</script>

<template>
  <div v-if="open" class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">
    <div class="bg-white rounded-lg shadow-xl w-80 p-6 space-y-4">
      <h2 class="text-lg font-semibold">{{ title || 'Confirm' }}</h2>
      <p class="text-sm text-gray-600">{{ message || 'Are you sure?' }}</p>

      <div class="flex justify-end gap-2">
        <button class="px-3 py-1 border rounded hover:bg-gray-50" @click="close">Cancel</button>
        <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700" @click="confirm">
          Yes
        </button>
      </div>
    </div>
  </div>
</template>
