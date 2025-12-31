<template>
    <div class="bg-secondary w-full rounded-b-lg p-5">
        <div class="grid grid-cols-7 place-items-center gap-x-2 gap-y-4">
            <div v-for="day in days" :key="day">
                <span class="text-secondary-content">{{ day }}</span>
            </div>
            <div v-for="date in calendarDates" :key="date.key" class="flex h-8 w-8 items-center justify-center">
                <button
                    class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold"
                    :class="date.isCurrentMonth ? 'text-secondary-content' : 'text-secondary-content/50'"
                    @click="selectspecificDate(date.key)"
                >
                    <span>{{ date.day }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import dayjs from 'dayjs';
import { computed } from 'vue';
import { useDateState } from '../../composables/useDateState';

interface Props {
    month: number;
}

const props = defineProps<Props>();
const { selectedYear, setSelectedDate } = useDateState();
const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const calendarDates = computed(() => {
    const year = selectedYear.value;
    const month = props.month;
    const firstDay = new Date(year, month, 1);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());

    const dates = [];
    for (let i = 0; i < 42; i++) {
        const currentDate = new Date(startDate);
        currentDate.setDate(startDate.getDate() + i);
        const key = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
        dates.push({
            day: currentDate.getDate(),
            month: currentDate.getMonth(),
            isCurrentMonth: currentDate.getMonth() === month,
            key: key,
        });
    }
    return dates;
});

const selectspecificDate = (date: string) => {
    const d = dayjs(date);
    setSelectedDate(d.year(), d.month(), d.date());
};
</script>

<style scoped></style>
