<script setup>
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import DatePicker from "@/Components/Common/DatePicker";
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
  if (editing.value) {
  }
};

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
      <template #title>{{editing ? 'Edit event' : 'Add new event'}}</template>
      <template #content>
        <Input name="title" label="Title" v-model="form.title" />
        <DatePicker label="Date" v-model="form.starts_at" />
      </template>
      <template #footer>
        <Button variant="secondary" class="mr-3" @click="onClose">Cancel</Button>
        <Button>Submit</Button>
      </template>
    </Dialog>
  </div>
</template>

<style scoped>
</style>
