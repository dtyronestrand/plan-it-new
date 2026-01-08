<template>
    <div class="align-center flex flex-row flex-wrap">
        <Weekday />

        <Someday class="grow" />
    </div>
    ]
</template>

<script setup lang="ts">
import Someday from './Someday.vue';
import Weekday from './Weekday.vue';
</script>

<style scoped></style>
