<template>
    <div v-if="props.open">
        <div
            class="fixed inset-0 z-40 bg-slate-700 opacity-50"
            @click="emit('close')"
        ></div>
        <div
            :class="[
                'fixed top-0 left-0 z-50 flex h-full max-h-dvh transform flex-col bg-black p-4 shadow-2xl transition-transform duration-300 ease-in-out md:w-96',
                props.open ? 'translate-x-0' : 'translate-x-full',
            ]"
        >
            <div class="lcars-row pt-8">
                <div class="lcars-text-box full-centered lcars-black-bg">
                    <ArrowLeftCircle
                        role="button"
                        @click="modifyYear(-1)"
                        class="size-12 text-white"
                    />
                </div>
                <div class="lcars-element button lcars-black-bg">
                    <div class="lcars-text-box full-centered large">
                        {{ selectedYear }}
                    </div>
                </div>
                <div class="lcars-text-box full-centered lcars-black-bg">
                    <ArrowRightCircle
                        role="button"
                        @click="modifyYear(1)"
                        class="size-12 text-white"
                    />
                </div>
            </div>
            <div class="calndar-container overflow-y-auto">
                <div
                    v-for="month in months"
                    :key="month.id"
                    class="mt-5 text-center font-bold text-black"
                >
                    <div class="lcars-row mb-4">
                        <span
                            class="lcars-bar horizontal left-end decorated lcars-mariner-bg"
                        ></span>
                        <span class="lcars-bar horizontal lcars-mariner-bg">
                            <span class="lcars-title">
                                {{ month.name }}
                            </span>
                        </span>
                        <span
                            class="lcars-bar horizontal right-end decorated lcars-mariner-bg"
                        ></span>
                    </div>
                    <Dates :month="month.id" @dateSelected="emit('close')" />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { useDateState } from '@/composables/useDateState';
import dayjs from 'dayjs';
import { ArrowLeftCircle, ArrowRightCircle } from 'lucide-vue-next';
import Dates from './Dates.vue';
const currentYear = dayjs().year();
const { selectedYear, setSelectedYear } = useDateState();

setSelectedYear(currentYear);

function modifyYear(v: number) {
    setSelectedYear(selectedYear.value + v);
}
interface Props {
    open: boolean;
}
const props = defineProps<Props>();
const emit = defineEmits(['close']);
const months = [
    { id: 0, name: 'January' },
    { id: 1, name: 'February' },
    { id: 2, name: 'March' },
    { id: 3, name: 'April' },
    { id: 4, name: 'May' },
    { id: 5, name: 'June' },
    { id: 6, name: 'July' },
    { id: 7, name: 'August' },
    { id: 8, name: 'September' },
    { id: 9, name: 'October' },
    { id: 10, name: 'November' },
    { id: 11, name: 'December' },
];
</script>
<style scoped>
.calndar-container {
    scrollbar-width: none;
}
.calndar-container::-webkit-scrollbar {
    display: none;
}
</style>
