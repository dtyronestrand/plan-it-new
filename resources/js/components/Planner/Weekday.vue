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
        <span v-if="allTasks.length > 0">
            <span v-for="task in getTasksForDate(day.format('YYYY-MM-DD'))" :key="task.id">
                <TaskComponent
                    :task="task"
                    :disabled="true"
                    @taskStatus="handleTaskStatus"
                />
            </span>
        </span>
        <TaskInput
            :disabled="false"
            :class="'decorated lcars-blue-color'"
            :due="day.format('YYYY-MM-DD')"
            :calendar="(page.props.calendar as Calendar)?.id"
            class="lcars-row lcars-u-2"
        />
        <span
            v-for="i in 8 - getTasksForDate(day.format('YYYY-MM-DD')).length - 1"
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
            <span v-if="allTasks.length > 0" class="col-span-2">
                <span v-for="task in getTasksForDate(endDay.format('YYYY-MM-DD'))" :key="task.id">
                    <TaskComponent
                        :task="task"
                        @taskStatus="handleTaskStatus"
                    />
                </span>
            </span>
            <TaskInput
                :disabled="false"
                :class="'decorated lcars-blue-color'"
                :due="endDay.format('YYYY-MM-DD')"
                :calendar="(page.props.calendar as any)?.id"
                class="col-span-2"
            />
            <span
                v-for="i in 3 - getTasksForDate(endDay.format('YYYY-MM-DD')).length - 1"
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
import { useDateState } from '@/composables/useDateState';
import type { Calendar, Task } from '@/types';
import { usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { computed, ref } from 'vue';
import TaskComponent from './Task.vue';
import TaskInput from './TaskInput.vue';

const page = usePage();
const { selectedYear, selectedMonth, selectedDate } = useDateState();

// Get tasks from the most reliable source
const allTasks = computed(() => {
    // Try calendar tasks first, then fallback to page tasks
    const calendarTasks = (page.props.calendar as Calendar)?.tasks as Task[];
    const pageTasks = page.props.tasks as Task[];
    return calendarTasks || pageTasks || [];
});

const weekView = computed(() => {
    const selectedDay = dayjs(
        `${selectedYear.value}-${selectedMonth.value + 1}-${selectedDate.value}`,
    );
    const startOfWeek = selectedDay.startOf('week').add(1, 'day'); // Adjust to Monday start

    return Array.from({ length: 7 }, (_, i) => startOfWeek.add(i, 'day'));
});

const weekDays = computed(() => weekView.value.slice(0, 5));
const weekEnd = computed(() => weekView.value.slice(5, 7));

// Helper function to get tasks for a specific date
const getTasksForDate = (date: string) => {
    return allTasks.value.filter((task) => {
        // Handle different date formats
        const taskDate = dayjs(task.due_date).format('YYYY-MM-DD');
        return taskDate === date;
    });
};

const selectedTask = ref(null);

const handleTaskStatus = (task: any) => {
    console.log('Task status changed:', task);
};
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
