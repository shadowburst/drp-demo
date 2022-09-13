<script setup>
import { computed, ref, watch } from "vue";
import moment from "moment";
import Button from "./Button";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
  modelValue: {
    type: Object,
    validator: moment.isMoment,
  },
  label: String,
});

// The current month displayed in the datepicker popup
const displayedMonth = ref(props.modelValue || moment());

// Every time the selected value changes, update the displayed month to match it if possible
watch(
  () => props.modelValue,
  () => {
    displayedMonth.value = props.modelValue
      ? props.modelValue.clone()
      : moment();
  }
);

// Get a list of all weeks in the displayed month
// Each week contains 7 moment instances, representing each day of said week
const weeks = computed(() => {
  // Get the first and last days that will be displayed
  // Note that some days can be outside the displayed month
  const startDate = displayedMonth.value
    .clone()
    .startOf("month")
    .startOf("week")
    .startOf("day");
  const endDate = displayedMonth.value
    .clone()
    .endOf("month")
    .endOf("week")
    .endOf("day");

  const weeks = [];
  // For each week in the month, add 7 moment instances
  for (let i = 0; i < Math.abs(startDate.diff(endDate, "days")) / 7; i++) {
    weeks.push(
      Array.from([0, 1, 2, 3, 4, 5, 6], (j) => {
        return startDate.clone().add(i, "weeks").add(j, "days");
      })
    );
  }

  return weeks;
});

// Represents whether or not the datepicker popup is open
const open = ref(false);

const handleDateClick = (day) => {
  // Use a timeout to handle the click after the blur event that will be called systematically
  setTimeout(() => {
    emit("update:modelValue", day);
    open.value = false;
  }, 0);
};

const handleBlur = (event) => {
  // Check if the clicked item was inside the current component
  if (!event.currentTarget.contains(event.relatedTarget)) {
    open.value = false;
  } else {
    event.currentTarget.focus();
  }
};
</script>

<template>
  <label class="block font-medium text-sm text-gray-700">
    <span v-if="label">{{ label }}</span>
  </label>
  <div class="relative">
    <button
      class="mb-6 px-3 h-10 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm text-left"
      @click="open = true"
      @blur="handleBlur"
    >
      {{modelValue?.format('DD/MM/YYYY')}}
      <div v-show="open" class="absolute top-1 card p-2 cursor-default">
        <div class="flex flex-row justify-between items-center">
          <button
            class="button"
            @click="displayedMonth = displayedMonth.clone().subtract(1, 'month')"
          >
            <vue-feather type="chevron-left" />
          </button>
          <div>{{displayedMonth.format('MMMM YYYY')}}</div>
          <button class="button" @click="displayedMonth = displayedMonth.clone().add(1, 'month')">
            <vue-feather type="chevron-right" />
          </button>
        </div>
        <div v-for="(week, weekIndex) in weeks" :key="`week_${weekIndex}`" class="flex flex-row">
          <div v-for="(day, dayIndex) in week" :key="`week_${weekIndex}_day_${dayIndex}`">
            <button
              class="button"
              :class="{'opacity-25': !day.isSame(displayedMonth, 'month'), 'button--selected': modelValue && day.isSame(modelValue, 'day')}"
              @click="handleDateClick(day)"
            >{{day.format('DD')}}</button>
          </div>
        </div>
      </div>
    </button>
  </div>
</template>

<style lang="postcss" scoped>
.button {
  @apply rounded-full h-10 w-10 flex justify-center items-center text-gray-700 hover:bg-gray-100 cursor-pointer;
}

.button--selected {
  @apply bg-gray-800 hover:bg-gray-700 text-gray-50;
}
</style>
