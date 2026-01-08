<template>
    <div
        :class="{ done: props.task.done }"
        class="text-base-content group col-span-2 flex cursor-pointer justify-between"
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
import type { Calendar, Task } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import Checkmark from './Checkmark.vue';
import TaskModal from './TaskModal.vue';

interface Props {
    task: Task;
}
const props = defineProps<Props>();


const selectedTask = ref<Task | null>(null);
const page = usePage();

const checkedValue = computed(() => props.task.done);
const handleTaskStatus = () => {
    const updatedTask = { ...props.task, done: !props.task.done };
    handleTask(updatedTask);
};

const handleTask = (updatedTask: Task) => {
    const requestData = {
        id: updatedTask.id,
        name: updatedTask.name,
        due_date: updatedTask.due_date,
        notes: updatedTask.notes,
        sub_tasks: JSON.stringify(updatedTask.sub_tasks),
        user_id: page.props.auth.user.id,
        calendar_id: (page.props.calendar as Calendar).id,
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




</script>

<style scoped>
.done {
    text-decoration: line-through;
    color: var(--color-neutral-300);
}
</style>
