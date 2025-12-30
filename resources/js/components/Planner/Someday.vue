<template>
    <div class="col-span-6">
        <h2 class="text-base-content mx-5 border-b-4 border-base-content text-5xl mb-4">Someday</h2>
        <div class="mx-5">
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div v-for="task in taskList" :key="task.id" role="button">
                    <Task :task="task" @taskStatus="handleTaskStatus"/>
                </div>
                <TaskInput v-for="i in (3 - (taskList.length % 3)) % 3" :key="'empty-' + i" :disabled="true" :due="null" :calendarId="page.props.calendar.id" />
            </div>
            <div class="grid grid-cols-3 gap-4">
                <TaskInput :disabled="false" :due="null" :calendarId="page.props.calendar.id" />
                <TaskInput v-for="i in (9-taskList.length)" :key="i" :disabled="true" :due="null" :calendarId="page.props.calendar.id" />
            </div>
        </div>
    </div>
 

</template>

<script setup lang="ts">
import { router, useForm, usePage } from "@inertiajs/vue3"
import { computed, ref } from "vue"
import Task from "./Task.vue"
import TaskInput from "./TaskInput.vue"

const page = usePage()

const taskList = computed(() => {
	return (
		page.props.tasks?.filter(
			(task) => task.due_date === null || task.due_date === "",
		) || []
	)
})
const selectedTask = ref(null)

const form = useForm({
	name: "",
	due_date: "",
	done: false,
	notes: "",
	subtasks: [],
	user_id: page.props.auth.user.id,
	calendar_id: page.props.calendar.id,
})
const handleTaskStatus = (updatedTask) => {
	console.log("handleTaskStatus called with:", updatedTask)
	router.put(
		`/tasks/${updatedTask.id}`,
		{
			id: updatedTask.id,
			name: updatedTask.name,
			due_date: updatedTask.due_date,
			notes: updatedTask.notes,
			subtasks: updatedTask.subtasks,
			user_id: page.props.auth.user.id,
			calendar_id: page.props.calendar.id,
			done: updatedTask.done,
		},
		{
			onSuccess: () => {
				router.reload()
			},
			onError: () => {
				// Handle error
			},
		},
	)
}
const submitForm = () => {
	if (selectedTask.value) {
		form.name = selectedTask.value.name
		form.due_date = selectedTask.value.due_date
		form.subtasks = Array.isArray(selectedTask.value.subtasks)
			? selectedTask.value.subtasks
			: []
		form.user_id = page.props.auth.user.id
		form.calendar_id = page.props.calendar.id

		form.put(`/tasks/${selectedTask.value.id}`)
	}
}

const openModal = (task) => {
	selectedTask.value = { ...task, subtasks: task.subtasks || [] }
}
const closeModal = () => {
	selectedTask.value = null
}

const addSubtask = () => {
	if (selectedTask.value) {
		selectedTask.value.subtasks.push({ name: "", done: false }) // Add a new subtask with default values
	}
}

const removeSubtask = (index: number) => {
	if (selectedTask.value) {
		selectedTask.value.subtasks.splice(index, 1)
	}
}
</script>

<style scoped></style>
