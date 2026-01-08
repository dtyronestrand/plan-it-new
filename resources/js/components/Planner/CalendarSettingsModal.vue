<template>
    <div
        v-if="props.settingsOpen"
        class="bg-black/70 fixed inset-0 flex h-screen w-full items-center justify-center"
        @click.self="close"
    >
        <div
            class="lcars-container w-full max-w-2xl"
        ><div class="lcars-row">
        <div class="lcars-bar horizontal left-end decorated "></div>
            <div class="lcars-bar horizontal"></div>
            <div class="lcars-elbow  right-bottom "></div>
        </div>
            <button
                aria-label="close"
                class="text-base-content absolute top-0 right-0 mx-5 my-2 text-xl"
                @click.prevent="close"
            >
                &times;
            </button>
            <div class=" bg-black">
            <div class="lcars-row">
          
                <h1
                    class="lcars-element-addition text-base-content text-center text-5xl pl-4 pr-32 font-bold"
                >
                    Calendar Settings
                </h1>
                  <div class="lcars-element ">
            </div>
            </div>
            <div class="lcars-row">
            <div class="lcars-element lcars-vu-2"></div>
                <h2 class="lcars-element-addition text-base-content pt-4 pl-4 mb-4 text-3xl font-bold">
                    Members
                </h2>
                    </div>
                <div class="justify-between flex w-full flex-row gap-4">
                    <div>
                        <p>{{ page.props.auth.user.name }}</p>
                        <p class="text-sm">{{ page.props.auth.user.email }}</p>
                    </div>
                    <div>
                        <p
                            class="bg-accent text-accent-content rounded-2xl px-2 text-sm"
                        >
                            Owner
                        </p>
            </div>
                </div>
                <hr class="my-4" />
                <button class="btn btn-primary mt-4 rounded-4xl">
                    Add Member
                </button>
                <hr class="my-4" />
                <button
                    class="btn btn-error mx-auto mt-4 block rounded-4xl"
                    popovertarget="confirm-delete"
                    popovertargetaction="show"
                >
                    Delete Calendar
                </button>
                <div
                    ref="deletePopover"
                    class="bg-base-300 text-base-content popover relative rounded-4xl p-10"
                    popover="manual"
                    id="confirm-delete"
                >
                    <h2 class="pb-5">
                        Are you sure you want to delete this calendar?
                    </h2>
                    <div class="mt-4 flex flex-row justify-center gap-4">
                        <button
                            type="button"
                            class="btn btn-error rounded-4xl"
                            @click="deleteCalendar"
                        >
                            Yes, Delete
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary rounded-4xl"
                            popovertarget="confirm-delete"
                            popovertargetaction="hide"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import type {Calendar} from '@/types';
interface Props {
    settingsOpen: boolean;
    calendar: Calendar | null;
}

const props = defineProps<Props>();
const page = usePage();

const popover = useTemplateRef('deletePopover');
const emit = defineEmits(['close', 'deleteCalendar']);
function close() {
    emit('close');
}

function deleteCalendar() {
    if (popover.value) {
        popover.value.hidePopover();
    }
    emit('deleteCalendar');
    close();
}
</script>

<style scoped>
.popover {
    margin: 0;
    inset: auto;
    left: calc(anchor(left) + 5px);
}
</style>
