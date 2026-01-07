<template>
    <div
        :class="{ done: props.task.done }"
        class="text-base-content group col-span-2 flex cursor-pointer justify-between  "
    >
        <p @click="openModal(props.task)" role="button">
            {{ props.task.name }}
        </p>
        <Checkmark
            :checked="checkedValue"
            type="checkbox"
            class="opacity-0 group-hover:opacity-100"
            @updateChecked="handleTaskStatus"
        />
    </div>
    <!-- Modal -->
    <TaskModal
        v-if="selectedTask"
        :task="selectedTask"
        @close="closeModal"
        @updateTask="handleTask"
    />
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useDateState } from '../../composables/useDateState';
import type { Task , Calendar} from '@/types';
import Checkmark from './Checkmark.vue';
import TaskModal from './TaskModal.vue';

interface Props {
    task: Task;
}
const props = defineProps<Props>();

const { selectedYear, selectedMonth, selectedDate, setSelectedDate } =
    useDateState();
const selectedTask = ref<Task|null>(null);
const page = usePage();

const checkedValue = computed(() => props.task.done);
const handleTaskStatus = () => {
    const updatedTask = { ...props.task, done: !props.task.done };
    handleTask(updatedTask);
};
const emit = defineEmits(['taskStatus']);
const handleTask = (updatedTask: Task) => {
    const requestData = {
        id: updatedTask.id,
        name: updatedTask.name,
        due_date: updatedTask.due_date,
        notes: updatedTask.notes,
        sub_tasks: JSON.stringify(updatedTask.sub_tasks),
        user_id: page.props.auth.user.id,
        calendar_id: (page.props.calendar as Calendar).id ,
        done: updatedTask.done,
    };
    console.log('Sending request data:', requestData);
    router.put(`/tasks/${updatedTask.id}`, requestData, {
        onError: () => {
            console.error('Error updating task');
        },
    });
};

const openModal = (task: Task) => {
    selectedTask.value = { ...task };
};

const closeModal = () => {
    selectedTask.value = null;
};

const addSubtask = () => {
    if (selectedTask.value) {
        selectedTask.value.sub_tasks = selectedTask.value.sub_tasks || [];
        const newSubtask: Task = {
            id: Date.now(), // temporary ID
            name: '',
            done: false,
            notes: null,
            calendar_id: selectedTask.value.calendar_id,
            due_date: null,
            sub_tasks: [],
            attachments: []
        };
        selectedTask.value.sub_tasks.push(newSubtask);
    }
};

const removeSubtask = (index: number) => {
    if (selectedTask.value && selectedTask.value.sub_tasks) {
        selectedTask.value.sub_tasks.splice(index, 1);
    }
};

const submitForm = () => {
    closeModal();
};
</script>

<style scoped>
.done {
    text-decoration: line-through;
    color: var(--color-neutral-300);
}
</style>
