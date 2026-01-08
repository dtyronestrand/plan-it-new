<template>
    <input
        :class="props.class"
        class="lcars-text-input"
        v-model="taskName"
        :disabled="props.disabled"
        :calendarId="props.calendar"
        :due="props.due"
        type="text"
        @blur="handleBlur"
    />
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Props {
    task?: string;
    class?: string;
    disabled?: boolean;
    due?: string | null;
    completed?: boolean;
    calendar: number;
}
const props = defineProps<Props>();
const page = usePage();
const taskName = ref(props.task || '');

const calendarId = ref(props.calendar);
const user = ref(page.props.auth.user.id);
watch(
    () => [taskName.value, props.due, props.completed, props.calendar],
    ([name, due, completed, calendarId]) => {
        console.log('Component values:', {
            taskName: name,
            due,
            completed,
            calendarId,
        });
    },
    { immediate: true },
);



const handleBlur = () => {
    if (taskName.value.trim()) {
        router.post(
            '/tasks',
            {
                name: taskName.value,
                due_date: props.due,
                done: props.completed,
                calendar_id: calendarId.value,
                user_id: user.value,
            },
            {
                onSuccess: () => {
                    taskName.value = '';
                    router.reload();
                },
            },
        );
    }
};
</script>

<style scoped></style>
