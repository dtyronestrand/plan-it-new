<template>
    <div v-if="settingsOpen" class=" fixed inset-0 flex h-screen w-full items-center justify-center bg-base-100/40" @click.self="close">
        <div class="bg-base-300 relative w-full max-w-2xl rounded-4xl p-10 shadow-lg">
            <button aria-label="close" class="text-base-content absolute right-0 top-0 mx-5 my-2 text-xl" @click.prevent="close">&times;</button>
            <div class="p-5">
        <h1 class="text-base-content text-center text-5xl font-bold mb-12 ">Calendar Settings</h1>
        <h2 class="text-base-content text-3xl font-bold mb-4 ">Members</h2>
        <div class="flex w-full flex-row jutify-between gap-4">
        <div >
        <p>{{ page.props.auth.user.name }}</p>
        <p class="text-sm ">{{ page.props.auth.user.email }}</p>
        </div>
        <div >
        <p class="px-2 text-sm bg-accent text-accent-content rounded-2xl">Owner</p>
        </div>
        </div>
        <hr class="my-4"/>
        <button class="btn btn-primary rounded-4xl mt-4">Add Member</button>
        <hr class="my-4"/>
         <button class="btn btn-error rounded-4xl mt-4 mx-auto block" popovertarget="confirm-delete" popovertargetaction="show">Delete Calendar</button>
         <div ref="deletePopover" class="bg-base-300 text-base-content relative p-10 rounded-4xl popover" popover="manual" id="confirm-delete">
         <h2 class="pb-5">Are you sure you want to delete this calendar?</h2>
            <div class="flex flex-row gap-4 justify-center mt-4">
            <button type="button" class="btn btn-error rounded-4xl" @click="deleteCalendar">Yes, Delete</button>
            <button type="button" class="btn btn-secondary rounded-4xl" popovertarget="confirm-delete" popovertargetaction="hide">Cancel</button>
            </div>
         </div>
    </div>
   
        </div>
    </div>
</template>

<script setup lang="ts">
import { usePage } from "@inertiajs/vue3"
import { onMounted, ref, useTemplateRef } from "vue"

interface Props {
	settingsOpen: boolean
	calendar: {
		id: number
		name: string
		description: string
		color: string
		is_default: boolean
		is_active: boolean
		user_id: number
		members: Array<{ id: number; name: string; email: string }>
	} | null
}

const props = defineProps<Props>()
const page = usePage()

const popover = useTemplateRef("deletePopover")
const emit = defineEmits(["close", "deleteCalendar"])
function close() {
	emit("close")
}

function deleteCalendar() {
	if (popover.value) {
		popover.value.hidePopover()
	}
	emit("deleteCalendar")
	close()
}
</script>

<style scoped>
.popover {
    margin: 0;
    inset: auto;
    left: calc(anchor(left) + 5px);
 
}
</style>