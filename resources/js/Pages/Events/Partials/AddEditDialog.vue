<script setup>
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import DateTimePicker from "@/Components/Common/DateTimePickers/DateTimePicker";
import { useForm } from "@inertiajs/inertia-vue3";
import { ref, watch } from "vue";
import moment from "moment";

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

const onAddNew = () => {
  form.reset();
  show.value = true;
  editing.value = false;
};

const onSubmit = () => {
  const transform = (data) => ({
    ...data,
    starts_at: data.starts_at.format("YYYY-MM-DD HH:mm"),
  });

  const requestParams = {
    preserveScroll: true,
    onSuccess: onClose,
  };

  if (editing.value) {
    form
      .transform(transform)
      .put(route("events.update", props.itemToEdit.id), requestParams);
  } else {
    form.transform(transform).post(route("events.store"), requestParams);
  }
};

const onClose = () => {
  form.reset();
  show.value = false;
  emit("close");
  console.log("closed");
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
