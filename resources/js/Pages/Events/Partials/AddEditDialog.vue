<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import moment from "moment";
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import DateTimePicker from "@/Components/Common/DateTimePickers/DateTimePicker";

const emit = defineEmits(["close"]);

const props = defineProps({
  itemToEdit: {
    type: Object,
    default: null,
  },
});

const show = ref(false);
const editing = ref(false);

const form = useForm({
  title: "",
  starts_at: null,
});

// Reset the form whenever the item to edit changes
watch(
  () => props.itemToEdit,
  () => {
    if (props.itemToEdit) {
      form.title = props.itemToEdit.title;
      form.starts_at = moment(props.itemToEdit.starts_at);
      form.clearErrors();
      show.value = true;
      editing.value = true;
    } else {
      form.reset();
      show.value = false;
    }
  }
);

// Called when the user clicks on the "Add new" button
const onAddNew = () => {
  form.reset();
  show.value = true;
  editing.value = false;
};

// Called when the user submits the form
const onSubmit = () => {
  const transform = (data) => ({
    ...data,
    starts_at: data.starts_at.format("YYYY-MM-DD HH:mm"),
  });

  const requestParams = {
    preserveScroll: true,
    onSuccess: onClose,
  };

  // Stores or updates the item
  if (editing.value) {
    form
      .transform(transform)
      .put(route("events.update", props.itemToEdit.id), requestParams);
  } else {
    form.transform(transform).post(route("events.store"), requestParams);
  }
};

// Called when the dialog closes
const onClose = () => {
  form.reset();
  show.value = false;
  emit("close");
};
</script>

<template>
  <div>
    <Button @click="onAddNew">
      <vue-feather type="plus" />
      <span class="ml-2">Add new</span>
    </Button>
    <Dialog :show="show" @close="onClose">
      <template #header>{{editing ? 'Edit event' : 'Add new event'}}</template>

      <Input name="title" label="Title" v-model="form.title" class="mb-6" />
      <DateTimePicker label="Date" v-model="form.starts_at" class="mb-6" />

      <template #footer>
        <Button variant="secondary" class="mr-3" @click="onClose">Cancel</Button>
        <Button @click="onSubmit">Submit</Button>
      </template>
    </Dialog>
  </div>
</template>

<style scoped>
</style>
