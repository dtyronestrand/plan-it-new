<template>
    <div
        v-for="(day, index) in weekDays"
        :key="day.format('YYYY-MM-DD')"
        class="lcars-column mx-4"
    >
        <div
            class="mt-5 mb-5 grid grid-cols-2 grid-rows-1 border-b-4 border-indigo-500"
        >
            <p>{{ day.format('dddd') }}</p>
            <p class="justify-self-end">{{ day.format('D') }}</p>
        </div>
        <span
            v-if="
                page.props.tasks &&
                Array.isArray(page.props.tasks) &&
                page.props.tasks.length > 0
            "
        >
            <span v-for="(task, taskIndex) in page.props.tasks" :key="task.id">
                <div v-if="task.due_date === day.format('YYYY-MM-DD')">
                    <TaskComponent
                        :task="task"
                        @taskStatus="handleTaskStatus"
                    />
                </div>
            </span>
        </span>
        <TaskInput
            :disabled="false"
            :due="day.format('YYYY-MM-DD')"
            :calendar="(page.props.calendar as Calendar)?.id"
            class="lcars-row lcars-u-2"
        />
        <span
            v-for="i in 8 -
            (Array.isArray(page.props.tasks) ? page.props.tasks.filter(
                (task) => task.due_date === day.format('YYYY-MM-DD'),
            )?.length || 0 : 0) -
            1"
            :key="i"
            class="col-span-2"
        >
            <TaskInput
                :disabled="true"
                :due="day.format('YYYY-MM-DD')"
                :calendar="(page.props.calendar as any)?.id"
                class="lcars-u-2"
            />
        </span>
    </div>

    <div class="weekend-container">
        <div
            v-for="(endDay, endIndex) in weekEnd"
            :key="endDay.format('YYYY-MM-DD')"
            :class="`day-${endIndex + 5} lcars-column`"
            class="ml-4"
        >
            <div
                class="mt-5 mb-5 grid grid-cols-2 grid-rows-1 border-b-4 border-indigo-500"
            >
                <p>{{ endDay.format('dddd') }}</p>
                <p class="justify-self-end">{{ endDay.format('D') }}</p>
            </div>
            <span
                v-if="
                    page.props.tasks &&
                    Array.isArray(page.props.tasks) &&
                    page.props.tasks.length > 0
                "
                class="col-span-2"
            >
                <span v-for="task in page.props.tasks" :key="task.id">
                    <span v-if="task.due_date === endDay.format('YYYY-MM-DD')">
                        <div
                            role="button"
                            class="border-base-content/10 col-span-2 border-b"
                            :taskName="task"
                            :disabled="true"
                        >
                            {{ task.name }}
                        </div>
                    </span>
                </span>
            </span>
            <TaskInput
                :disabled="false"
                :due="endDay.format('YYYY-MM-DD')"
                :calendar="(page.props.calendar as any)?.id"
                class="col-span-2"
            />
            <span
                v-for="i in 3 -
                ((taskList[endIndex + 5]?.tasks as any[])?.length || 0) -
                1"
                :key="i"
                class="col-span-2"
            >
                <TaskInput
                    :disabled="true"
                    :due="endDay.format('YYYY-MM-DD')"
                    :calendar="(page.props.calendar as any)?.id"
                />
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router, useForm, usePage } from "@inertiajs/vue3"
import dayjs from "dayjs"
import { computed, ref, watchEffect } from "vue"
import { useDateState } from "@/composables/useDateState"
import type { Calendar } from "@/types"
import TaskComponent from "./Task.vue"
import TaskInput from "./TaskInput.vue"

const page = usePage()
const { selectedYear, selectedMonth, selectedDate } = useDateState()

const weekView = computed(() => {
	const selectedDay = dayjs(
		`${selectedYear.value}-${selectedMonth.value + 1}-${selectedDate.value}`,
	)
	const startOfWeek = selectedDay.startOf("week").add(1, "day") // Adjust to Monday start

	return Array.from({ length: 7 }, (_, i) => startOfWeek.add(i, "day"))
})
const weekDays = computed(() => weekView.value.slice(0, 5))
const weekEnd = computed(() => weekView.value.slice(5, 7))
const taskList = computed(() => {
	return weekView.value.map((day) => ({
		day: day.format("YYYY-MM-DD"),
		tasks:
			((page.props.calendar as Calendar)?.tasks as any[])?.filter(
				(task) => task.due === day.format("YYYY-MM-DD"),
			) || [],
	}))
})

const selectedTask = ref(null)

const handleTaskStatus = (task: any) => {
	// Handle task status change
	console.log("Task status changed:", task)
}
</script>

<style scoped>
@reference "../../../css/app.css";

.weekend-container {
    display: flex;
    flex-direction: column;
}

.done {
    text-decoration: line-through;
    color: #888888;
}
</style>
