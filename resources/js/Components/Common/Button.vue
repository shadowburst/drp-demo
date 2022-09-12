<script setup>
const emit = defineEmits(["click"]);
defineProps({
  type: {
    type: String,
    default: "button",
  },
  variant: {
    type: String,
    validator: (prop) => ["primary", "secondary"].includes(prop),
    default: "primary",
  },
});

const classes = {
  primary:
    "bg-gray-800 border border-transparent text-white hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300",
  secondary:
    "bg-white border border-gray-300 text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50",
};
</script>

<template>
  <button
    :type="type"
    @click="() => emit('click')"
    class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest disabled:opacity-25 transition"
    :class="classes[variant]"
  >
    <slot />
  </button>
</template>
