<script setup>
defineProps({
  data: {
    type: Array,
    default() {
      return [];
    },
  },
  headings: {
    type: Array,
    default() {
      return [];
    },
  },
});
</script>

<template>
  <table class="w-full text-sm text-left text-gray-500">
    <thead class="text-gray-700 uppercase bg-gray-50">
      <tr>
        <th v-for="(heading, headingIndex) in headings" :key="`heading_${headingIndex}`">{{heading}}</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="(row, rowIndex) in data"
        :key="`row_${rowIndex}`"
        class="border-b"
        :class="{'bg-white': rowIndex % 2 === 0, 'bg-gray-50': rowIndex % 2 === 1}"
      >
        <slot name="row" :item="row" />
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
</style>
