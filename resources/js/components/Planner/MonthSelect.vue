<template>
    <div v-if="props.open" class="z-40" @click="emit('close')">
        <div :class="['fixed top-0 left-0 z-50 flex h-full max-h-dvh w-full transform flex-col bg-black transition-transform duration-300 ease-in-out md:w-96', open ? 'translate-x-0' :'translate-x-full']">
            
        </div>
    </div>
    </template>
    <script setup lang="ts">




interface Props {
    open: boolean;
}
const props = defineProps<Props>();
    const emit = defineEmits(['close']);

        </script>
        <style scoped>

        </style>