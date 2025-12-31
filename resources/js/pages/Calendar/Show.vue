<template>
    <div class="lcars-app-container">
        <div class="lcars-row">
            <div class="lcars-column">
                <div class="lcars-element"></div>
            </div>
        </div>
        <div class="lcars-row">
            <div class="lcars-column">
                <div class="lcars-element lcars-periwinkle-bg"></div>
            </div>
        </div>
        <div class="lcars-row">
            <div class="lcars-column">
                <div class="lcars-elbow left-top lcars-blue-bg"></div>
            </div>
            <div class="lcars-bar horizontal lcars-dodger-blue-bg bottom">
                <input
                    ref="calendarInput"
                    type="text"
                    name="calendarName"
                    v-model="calendarName"
                    :readonly="!isEditable"
                    @click="handleClick"
                    @blur="handleBlur"
                    class="lcars-title"
                />
            </div>
            <div
                class="lcars-bar lcars-blue-bg horizontal bottom right-end decorated"
            ></div>
        </div>
        <div class="lcars-row">
            <div class="lcars-elbow lcars-dodger-blue-bg left-bottom"></div>
            <div class="lcars-bar lcars-blue-bg horizontal">
                <div class="lcars-title right">{{ month }}</div>
            </div>
            <div
                class="lcars-bar horizontal right-end decorated lcars-periwinkle-bg"
            ></div>
        </div>
        <div id="left-menu" class="lcars-column start-space lcars-u-1">
            <div
                class="lcars-element button lcars-dodger-blue-bg"
                @click="monthSelectOpen = true"
            >
                <div class="lcars-text-box full-centered lcars-dodger-blue-bg">
                    <span class="text-center text-black">Select Month</span>
                </div>
            </div>
            <div
                class="lcars-element button lcars-blue-bg text-center"
                @click="lastWeek"
            >
                <div class="lcars-text-box full-centered lcars-blue-bg">
                    <CircleArrowLeft class="size-10 text-black" />
                </div>
            </div>
            <div class="lcars-element button" @click="nextWeek">
                <div class="lcars-text-box full-centered lcars-golden-tanoi-bg">
                    <CircleArrowRight class="size-10 text-black" />
                </div>
            </div>
            <div class="lcars-element lcars-vu-4"></div>
            <div class="lcars-element button lcars-periwinkle-bg">Settings</div>
            <div
                class="lcars-element button lcars-red-alert-bg"
                @click="logout"
            >
                Logout
            </div>
        </div>

        <div id="container">
            <Planner />
            <MonthSelect
                :open="monthSelectOpen"
                @close="monthSelectOpen = false"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import Planner from '@/components/Planner/Index.vue';
import MonthSelect from '@/components/Planner/MonthSelect.vue';
import { useDateState } from '@/composables/useDateState';
import { getInitials } from '@/composables/useInitials';
import type { Calendar } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { CircleArrowLeft, CircleArrowRight } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const page = usePage();
const { selectedYear, selectedMonth, selectedDate, setSelectedDate } =
    useDateState();

const displayedCalendar = computed(() => page.props.calendar as Calendar);

const calendarName = ref<string>((page.props.calendar as Calendar).name);

const selectedCalendarId = ref((page.props.calendar as Calendar).id);

const calendarToEdit = ref<Calendar | null>(null);
const monthSelectOpen = ref(false);

watch(
    () => page.props.calendar as Calendar,
    (newCalendar: Calendar) => {
        calendarName.value = newCalendar.name;
        selectedCalendarId.value = newCalendar.id;
    },
);

const initials = getInitials(page.props.auth.user.name);

const updateCalendar = (calendar: Calendar) => {
    router.get(`/calendar/${calendar.user_id}/${calendar.id}`);
};

const deleteCalendar = (calendarId: number) => {
    if (!calendarId) return;
    router.delete(`/calendar/${calendarId}`, {
        onSuccess: () => {
            // After deletion, redirect to the default calendar or another appropriate page
            const defaultCalendar = (
                page.props.auth.user as any
            ).calendars?.find((cal: any) => cal.is_default);
            if (defaultCalendar) {
                router.get(
                    `/calendars/${page.props.auth.user.id}/${defaultCalendar.id}`,
                );
            } else {
                router.get(`/calendars/${page.props.auth.user.id}`); // Redirect to a general calendars page
            }
        },
        onError: (errors: any) => {
            console.error('Failed to delete calendar:', errors);
        },
    });
};
const isEditable = ref(false);
const calendarInput = ref<HTMLInputElement | null>(null);

const handleClick = () => {
    if (!isEditable.value) {
        isEditable.value = true;
        setTimeout(() => calendarInput.value?.focus(), 0);
    }
};
const handleBlur = () => {
    if (isEditable.value) {
        isEditable.value = false;
        if (calendarName.value.trim() !== displayedCalendar.value.name.trim()) {
            console.log(
                'Updating calendar:',
                (page.props.calendar as Calendar).id,
                'with name:',
                calendarName.value,
            );
            router.put(
                `/calendars/${displayedCalendar.value.user_id}/${displayedCalendar.value.id}`,
                { name: calendarName.value },
            );
        }
    }
};
const openSettingsModal = (calendar: Calendar) => {
    calendarToEdit.value = calendar;
};

const createCalendar = ref(false);

const lastWeek = () => {
    const currentDate = new Date(
        selectedYear.value,
        selectedMonth.value,
        selectedDate.value,
    );
    const lastWeekDate = dayjs(currentDate).subtract(7, 'day');
    setSelectedDate(
        lastWeekDate.year(),
        lastWeekDate.month(),
        lastWeekDate.date(),
    );
};

const nextWeek = () => {
    const currentDate = new Date(
        selectedYear.value,
        selectedMonth.value,
        selectedDate.value,
    );
    const nextWeekDate = dayjs(currentDate).add(7, 'day');
    setSelectedDate(
        nextWeekDate.year(),
        nextWeekDate.month(),
        nextWeekDate.date(),
    );
};

// Remove unused auth object, since user data comes from $page.props.auth.user
const logout = () => {
    router.post(
        '/logout',
        {},
        {
            onSuccess: () => {
                router.get('/login');
            },
        },
    );
};

const month = computed(() => {
    const months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];
    console.log(
        'Debug - selectedMonth.value:',
        selectedMonth.value,
        'showing:',
        months[selectedMonth.value],
    );
    return `${months[selectedMonth.value]} ${selectedYear.value}`;
});

const openCreateModal = () => {
    createCalendar.value = true;
};
</script>

<style scoped></style>
