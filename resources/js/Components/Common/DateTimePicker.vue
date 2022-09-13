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
  type: {
    type: String,
    validator: (val) => ["date", "datetime", "time"].includes(val),
    default: "datetime",
  },
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
const showPopup = ref(false);

const updateValue = (value) => {
  emit("update:modelValue", value);
};

const onBlur = (event) => {
  // Check if the clicked item was inside the current component
  if (!event.currentTarget.contains(event.relatedTarget)) {
    showPopup.value = false;
  } else {
    event.currentTarget.focus();
  }
};

// Whether or not time or date fields need to be shown
const hasTime = computed(() => props.type !== "date");
const hasDate = computed(() => props.type !== "time");

// Format to be used to show the selected value
const format = computed(() => {
  switch (props.type) {
    case "time":
      return "HH:mm";
    case "date":
      return "DD/MM/YYYY";
    default:
      return "HH:mm DD/MM/YYYY"; // datetime
  }
});
</script>

<template>
  <label class="block font-medium text-sm text-gray-700">
    <span v-if="label">{{ label }}</span>
  </label>
  <div class="relative">
    <button
      class="mb-6 px-3 h-10 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm text-left"
      @click="showPopup = true"
      @blur="onBlur"
    >
      {{modelValue?.format(format)}}
      <div v-show="showPopup" class="absolute top-1 card p-2 w-80 cursor-default">
        <template v-if="hasDate">
          <div class="mb-3 flex flex-row justify-between items-center">
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
          <div
            v-for="(week, weekIndex) in weeks"
            :key="`week_${weekIndex}`"
            class="flex flex-row justify-between"
          >
            <div v-for="(day, dayIndex) in week" :key="`week_${weekIndex}_day_${dayIndex}`">
              <button
                class="button"
                :class="{'opacity-25': !day.isSame(displayedMonth, 'month'), 'button--selected': modelValue && day.isSame(modelValue, 'day')}"
                @click="updateValue(modelValue ? modelValue.clone().set({ year: day.year(), month: day.month(), date: day.date() }) : day)"
              >{{day.format('DD')}}</button>
            </div>
          </div>
        </template>
        <div v-if="hasTime && modelValue" class="mt-2 flex flex-row justify-center items-center">
          <input
            type="number"
            class="number-input"
            :value="modelValue.format('HH')"
            @change="updateValue(modelValue.clone().hours($event.target.value))"
          />
          <span class="mx-2">:</span>
          <input
            type="number"
            class="number-input"
            :value="modelValue.format('mm')"
            @change="updateValue(modelValue.clone().minutes($event.target.value))"
          />
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

.number-input {
  @apply w-1/4 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm;
}
</style>
