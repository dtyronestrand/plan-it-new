<template>
    <div v-if="props.open" class="z-40" @click="emit('close')"></div>
        <div         :class="[
            'fixed top-0 left-0 z-50 flex h-full max-h-dvh w-full transform flex-col bg-black shadow-2xl transition-transform duration-300 ease-in-out md:w-96',
            open ? 'translate-x-0' : 'translate-x-full',
        ]">
            <div class="lcars-row">
                <div class="lcars-bracket top lcars-u-3 lcars-blue-color lcars-black-bg"></div>
            </div>
            <div class="lcars-row">
            
            <div class="lcars-element button left-rounded lcars-dodger-blue-bg" @click="modifyYear(-1)"><div class="lcars-text-box full-centered lcars-dodger-blue-bg"><ArrowLeftCircle class="size-14 text-black"/></div></div>
            <div class="lcars-element button lcars-blue-bg"><div class="lcars-text-box full-centered large lcars-blue-bg">{{selectedYear}}</div></div>
              <div class="lcars-element button right-rounded lcars-dodger-blue-bg" @click="modifyYear(1)"><div class="lcars-text-box full-centered lcars-dodger-blue-bg"><ArrowRightCircle class="size-14 text-black"/></div></div>
            </div>
            <div v-for="(month, index) in months" :key="month.id" :class="{'mt-5 : index !== 0'}" >{{ month.name }}</div>
            <Dates :month="month.id"/>
        </div>
   
    </template>
    <script setup lang="ts">

import dayjs from "dayjs"
import { useDateState } from "@/composables/useDateState"
import {ArrowLeftCircle, ArrowRightCircle} from 'lucide-vue-next';
import Dates from "./Dates.vue";
const currentYear = dayjs().year()
const { selectedYear, setSelectedYear } = useDateState()

setSelectedYear(currentYear)



function modifyYear(v: number) {
	setSelectedYear(selectedYear.value + v)
}
interface Props {
	open: boolean
}
const props = defineProps<Props>()
const emit = defineEmits(["close"])
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

        </style>