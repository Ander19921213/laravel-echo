<template>
    <div class="row">
        <div class="cols-md-8">
            <h4 v-text="project.name"></h4>
            <ul class="list-group max-h-[800px] overflow-auto" >
                <li class="list-group-item" v-for="task in project" v-text="task.body"></li>
            </ul>

            <input class="form-control" placeholder="new task" type="text" v-model="newTask" @blur="save"
                @keydown="onKeydown">
            <span v-if="activePeer" class="typing-message" v-text="activePeer.name + ` is typing...`"></span>
        </div>

        <div class="cols-md-4">
            <h2>Participants</h2>
            <!-- Add your chat component here -->

            <ul>
                <li v-for="participant in participants" v-text="participant.name"></li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const newTask = ref('');
const project = ref({});
const activePeer = ref(false);
const typingTimer = ref(false);
const participants = ref([]);

const props = defineProps({
    dataProject: {
        type: Object,
        required: true
    },
});

const addTask = (task) => {
    activePeer.value = false;
    project.value.push(task);
    newTask.value = '';
};

const flashActivePeer = (e) => {

    activePeer.value = e;

    if (typingTimer.value) {
        clearTimeout(typingTimer.value);
    }

    typingTimer.value = setTimeout(() => {
        activePeer.value = false;
    }, 3000);
};

const save = async () => {
    const res = await axios.post(`/api/projects/${props.dataProject.id}/tasks`, {
        body: newTask.value
    });

    addTask(res.data);
};

const onKeydown = (e) => {
    window.Echo.join('tasks.' + props.dataProject.id)
        .whisper('typing', {
            name: window.App.user.name,
        });
};

onMounted(async () => {
    window.Echo.join('tasks.' + props.dataProject.id)
        .here(users => {
            participants.value = users;

        })
        .joining(user => {
            participants.value.push(user);
        })
        .leaving(user => {
            participants.value.splice(participants.value.indexOf(user), 1);
        })
        .listen('TaskCreated', ({ task }) => {
            addTask(task)
        })
        .listenForWhisper('typing', flashActivePeer);

    project.value = props.dataProject.tasks;

});
</script>

<style scoped>
.row {
    display: flex;
    flex-direction: row;
}

.cols-md-8 {
    width: 66.6667%;
}

.cols-md-4 {
    width: 33.3333%;
}

.list-group {
    margin-bottom: 20px;
}

.list-group-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 10px;
}

.typing-message {
    font-style: italic;
}

ul {
    list-style-type: none;
    padding: 0;
}

</style>
