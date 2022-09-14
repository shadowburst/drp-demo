<script setup>
import moment from "moment";
import { computed, ref, watch } from "vue";

const props = defineProps({
  value: {
    type: Object,
    validator: moment.isMoment,
  },
  show: {
    type: Boolean,
    required: true,
  },
  withTime: {
    type: Boolean,
    default: true,
  },
  withDate: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(["change", "close"]);

// The current month displayed in the datepicker popup
const displayedMonth = ref(props.value || moment());

// Every time the selected value changes, update the displayed month to match it if possible
watch(
  () => props.value,
  () => {
    displayedMonth.value = props.value ? props.value.clone() : moment();
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

// used to update the "value" prop
const onChange = (value) => {
  emit("change", value?.clone());
};
</script>

<template>
  <div v-show="show" class="absolute top-0 card p-2 w-80 cursor-default">
    <template v-if="withDate">
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
            :class="{'opacity-25': !day.isSame(displayedMonth, 'month'), 'button--selected': value && day.isSame(value, 'day')}"
            @click="onChange(value ? value.set({ year: day.year(), month: day.month(), date: day.date() }) : day)"
          >{{day.format('DD')}}</button>
        </div>
      </div>
    </template>
    <div v-if="withTime && value" class="mt-2 flex flex-row justify-center items-center">
      <input
        type="number"
        class="number-input"
        :value="value.format('HH')"
        @change="onChange(value.hours($event.target.value))"
      />
      <span class="mx-2">:</span>
      <input
        type="number"
        class="number-input"
        :value="value.format('mm')"
        @change="onChange(value.minutes($event.target.value))"
      />
    </div>
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
