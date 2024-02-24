<template>
    <div>
        <h1>{{ props.project.name }}</h1>
        <TaskList :dataProject="props.project" />
    </div>
</template>
<script setup>
import TaskList from '@/Components/TaskList.vue';

const props = defineProps({
    project: {
        type: Object,
        required: true
    }
});

</script>