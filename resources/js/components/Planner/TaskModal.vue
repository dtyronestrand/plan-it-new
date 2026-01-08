<template>
<div v-if="task"
class="bg-black/50 fixed inset-0 z-50 flex items-center justify-center"
@click="closeModal">
<div class="lcars-container w-full max-w-min bg-black " @click.stop>
<div class="lcars-row lcars-u-5 ">
<div class="lcars-bar horizontal decorated left-end rounded"></div>
<div class="lcars-bar horizontal ">
<h1 class="lcars-title">Edit Task</h1>
</div>
</div>
<form @submit.prevent="saveForm" class="bg-black">
<div class="flex flex-row pt-4 pl-8 gap-8">
    <h2 class="text-2xl ">Task Name</h2>
        <input v-if="taskToUpdate"
        v-model="taskToUpdate.name"
        type="text"
       class="lcars-text-input decorated lcars-anakiwa-color"
        />
</div>
   
  
        <div v-if="taskToUpdate">
        <div class="flex flex-row pt-4 gap-8 pl-8">
   <h2 class="text-2xl mb-4">SubTasks</h2> 
    
    <button class="pl-4" type="button" @click.prevent="addSubtask"><Plus/></button>
        </div>
           <div
                        v-for="(subtask, index) in taskToUpdate.sub_tasks || []"
                        :key="index"
                        class="group pl-8 mb-2 flex items-center"
                    >
                        <input
                            v-model="subtask.name"
                            type="text"
                            :class="{ 'line-through': subtask.done }"
                            class="lcars-input decorated lcars-anakiwa-color mr-2  border-none"
                            placeholder="Subtask Name"
                        />
                        <input
                            :checked="subtask.done"
                            @change="subtask.done = !subtask.done"
                            type="checkbox"
                        />
                        <button
                            @click.prevent="removeSubtask(index)"
                            class="btn btn-sm btn-error"
                        >
                        <Trash2 class="text-[#e10] ml-4" /> 
                        </button>
                    </div>
        </div>
                  <div v-if="taskToUpdate">
                    <h2 class="text-2xl mb-2 pt-4 pl-8">Notes</h2>
                    <QuillEditor
                      theme="snow"
                        v-model:content="taskToUpdate.notes"
                        contentType="html"
                        class="mb-2 "
                    />
                </div>
</form>
</div>

</div>


</template>

<script setup lang="ts">
import { QuillEditor } from '@vueup/vue-quill';
import { ref } from 'vue';
import type {Task} from '@/types';
import {Plus, Trash2} from 'lucide-vue-next'
interface Props {
    task: Task;
}

const props = defineProps<Props>();
const emit = defineEmits(['close', 'updateTask']);
const taskToUpdate = ref<Task>(props.task); 
;

const addSubtask = () => {
    if (taskToUpdate.value) {
        if (!taskToUpdate.value.sub_tasks) {
            taskToUpdate.value.sub_tasks = [];
        }
        const newSubtask: Task = {
            id: Date.now(), // temporary ID
            name: '',
            done: false,
            notes: null,
            calendar_id: taskToUpdate.value.calendar_id,
            due_date: taskToUpdate.value.due_date,
            sub_tasks: [],
            attachments: []
        };
        taskToUpdate.value.sub_tasks.push(newSubtask);
    }
};

const removeSubtask = (index: number) => {
    if (taskToUpdate.value && taskToUpdate.value.sub_tasks) {
        taskToUpdate.value.sub_tasks.splice(index, 1);
    }
};

const saveForm = () => {
    if (taskToUpdate.value) {
        console.log('Saving task with notes:', taskToUpdate.value.notes);
        emit('updateTask', taskToUpdate.value);
        closeModal();
    }
};

const closeModal = () => {
    emit('close');
};
</script>

<style scoped>
    .lcars-blue-color{
  border-color:  #01e !important;
  background-color:black !important;

}
.lcars-row > * {
    margin-right: 0 !important;
}
</style>
