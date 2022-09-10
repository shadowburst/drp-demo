<script setup>
 const {events} = defineProps({
     events: {
         type: Array,
         default: []
     }
 })

 console.log(events)
</script>

<template>
    <table>
        <th>
            <td>Titre</td>
            <td>Date</td>
        </th>
        <tr v-for="(event, index) in events" :key="`event_${index}`">
            <td>{{event.title}}</td>
            <td>{{event.starts_at}}</td>
        </tr>
    </table>
</template>

<style scoped>
</style>
