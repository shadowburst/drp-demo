<script setup>
import AppLayout from "@/Layouts/AppLayout";
import AddEditDialog from "./Partials/AddEditDialog";
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Table from "@/Components/Common/Table";
import moment from "moment";
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";

defineProps({
  events: {
    type: Array,
    default: [],
  },
});

const itemToEdit = ref(null);
const itemToDelete = ref(null);

const onDelete = () => {
  Inertia.delete(route("events.delete", itemToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => (itemToDelete.value = null),
  });
};
</script>

<template>
  <AppLayout title="Events">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Events</h2>
    </template>

    <div class="card">
      <div class="mb-3">
        <AddEditDialog :item-to-edit="itemToEdit" @close="itemToEdit = null" />
        <Dialog :show="itemToDelete != null" @close="itemToDelete = null">
          <template #header>Deleting event</template>
          Are you sure you want to delete this event ?
          <template #footer>
            <Button variant="secondary" class="mr-3" @click="itemToDelete = null">Cancel</Button>
            <Button variant="danger" @click="onDelete">Submit</Button>
          </template>
        </Dialog>
      </div>
      <Table :data="events" :headings="['Title', 'Date', 'Actions']">
        <template #row="{item}">
          <td>{{item.title}}</td>
          <td>{{moment(item.starts_at).format('HH:mm DD/MM/YYYY')}}</td>
          <td>
            <span class="px-2 text-gray-700 hover:text-blue-500 cursor-pointer transition">
              <vue-feather type="edit" size="1.3rem" @click="itemToEdit = item" />
            </span>
            <span class="px-2 text-gray-700 hover:text-red-500 cursor-pointer transition">
              <vue-feather type="trash" size="1.3rem" @click="itemToDelete = item" />
            </span>
          </td>
        </template>
      </Table>
    </div>
  </AppLayout>
</template>

<style scoped>
</style>
