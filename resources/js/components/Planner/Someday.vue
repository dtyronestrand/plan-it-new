<template>
  <div class="grid">
        <div class="lcars-row">
            <div
                class="lcars-bar horizontal left-end decorated lcars-dodger-blue-bg"
            ></div>
            <div class="lcars-bar horizontal lcars-mariner-bg">
                <h2 class="lcars-title">Someday</h2>
            </div>
        </div>
        <div class="mx-5">
            <div class="mb-4 grid grid-cols-3 gap-4">
                <div v-for="task in taskList" :key="task.id" role="button">
                    <Task :task="task" @taskStatus="handleTaskStatus" />
                </div>
                <TaskInput
                    v-for="i in (3 - (taskList.length % 3)) % 3"
                    :key="'empty-' + i"
                    :disabled="true"
                    :due="null"
                    :calendar="(page.props.calendar as Calendar).id"
                />
            </div>
            <div class="grid grid-cols-3 gap-4">
                <TaskInput
                    :disabled="false"
                    :due="null"
                    :calendar="(page.props.calendar as Calendar).id"
                    :class="'decorated lcars-blue-color'"
                />
                <TaskInput
                    v-for="i in 9 - taskList.length"
                    :key="i"
                    :disabled="true"
                    :due="null"
                    :calendar="(page.props.calendar as Calendar).id"
                />
            </div>
        </div>
  </div>

</template>

<script setup lang="ts">
import type { Calendar, Task as TypeTask } from '@/types';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Task from './Task.vue';
import TaskInput from './TaskInput.vue';

const page = usePage();

const taskList = computed(() => {
    return (
        (page.props.tasks as TypeTask[])
            ?.filter((task) => task.due_date === null || task.due_date === '')
            .map((task) => ({
                ...task,
                notes: task.notes || '',
                date: task.due_date || '',
                sub_tasks: Array.isArray(task.sub_tasks)
                    ? task.sub_tasks
                    : [],
            })) || []
    );
});
const selectedTask = ref<any>(null);

const form = useForm<{
    name: string;
    due_date: string;
    done: boolean;
    notes: string;
    subtasks: TypeTask[];
    user_id: number;
    calendar_id: number;
}>({
    name: '',
    due_date: '',
    done: false,
    notes: '',
    subtasks: [] as TypeTask[],
    user_id: page.props.auth.user.id,
    calendar_id: (page.props.calendar as Calendar).id,
});
const handleTaskStatus = (updatedTask: any) => {
    console.log('handleTaskStatus called with:', updatedTask);
    router.put(
        `/tasks/${updatedTask.id}`,
        {
            id: updatedTask.id,
            name: updatedTask.name,
            due_date: updatedTask.due_date,
            notes: updatedTask.notes || '',
            subtasks: updatedTask.subtasks || [],
            user_id: page.props.auth.user.id,
            calendar_id: (page.props.calendar as Calendar).id,
            done: updatedTask.done,
        },
        {
            onSuccess: () => {
                router.reload();
            },
            onError: () => {
                // Handle error
            },
        },
    );
};
const submitForm = () => {
    if (selectedTask.value) {
        form.name = selectedTask.value.name;
        form.due_date = selectedTask.value.due_date || '';
        form.subtasks = Array.isArray(selectedTask.value.sub_tasks)
            ? selectedTask.value.sub_tasks
            : [];
        form.user_id = page.props.auth.user.id;
        form.calendar_id = (page.props.calendar as Calendar).id;

        form.put(`/tasks/${selectedTask.value.id}`);
    }
};

const openModal = (task: any) => {
    selectedTask.value = { ...task, subtasks: task.sub_tasks || [] };
};
const closeModal = () => {
    selectedTask.value = null;
};

const addSubtask = () => {
    if (selectedTask.value) {
        selectedTask.value.subtasks.push({ name: '', done: false }); // Add a new subtask with default values
    }
};

const removeSubtask = (index: number) => {
    if (selectedTask.value) {
        selectedTask.value.subtasks.splice(index, 1);
    }
};
</script>

<style scoped></style>
