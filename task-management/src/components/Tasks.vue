<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue'
import api from '@/utils/api'
import { useUserStore } from '@/stores/user'
import CreateTask from './Teleports/CreateTask.vue'
import UpdateTask from './Teleports/UpdateTask.vue'
import Echo from '@/echo'

const props = defineProps({
    teamId: {
        type: Number,
        required: true
    },
})

const userStore = useUserStore()


const showCreateTask = ref(false);
const showUpdateTask = ref(false);
const updateTaskId = ref(null)

const tasks = ref([])
const draggedTaskId = ref(null)
const isUpdating = ref(false)

const emit = defineEmits('re-fetch-data-numbers')
const reFetchDataNumber = () => emit("re-fetch-data-numbers");

/* ================== COMPUTED ================== */
const todoTasks = computed(() =>
    tasks.value.filter(task => task.status === 'todo')
)

const inProgressTasks = computed(() =>
    tasks.value.filter(task => task.status === 'in_progress')
)

const doneTasks = computed(() =>
    tasks.value.filter(task => task.status === 'done')
)

/* ================== DRAG ================== */
function dragStart(taskId) {
    if (isUpdating.value) return
    draggedTaskId.value = taskId
}

async function dropTask(newStatus) {
    if (draggedTaskId.value === null || isUpdating.value) return

    const task = tasks.value.find(t => t.id === draggedTaskId.value)
    if (!task) return

    const oldStatus = task.status

    // Optimistic UI
    task.status = newStatus
    draggedTaskId.value = null
    isUpdating.value = true

    try {
        await api.put(`/teams/${props.teamId}/tasks/${task.id}/status`, {
            status: newStatus
        })
    } catch (error) {
        console.error('Error updating task status:', error)

        // Rollback لو حصل error
        task.status = oldStatus
    } finally {
        isUpdating.value = false
    }
}

/* ================== FETCH ================== */
const fetchTasks = async () => {
    try {
        const { data } = await api.get(`/teams/${props.teamId}/tasks/`)
        tasks.value = data.data
    } catch (error) {
        console.error('Error fetching tasks:', error)
    }
}

const handleEditTask = (id) => {
    updateTaskId.value = id
    showUpdateTask.value = true
}

const handleDeleteTask = async (id) => {
    let check = confirm('Are you sure about deleting this message');

    if (!check) return;


    try {
        await api.delete(`/teams/${props.teamId}/tasks/${id}`)
        fetchTasks()
        reFetchDataNumber()
    } catch (error) {
        console.error('Error delete task:', error)
    }
}

let channel;

onMounted(() => {
    fetchTasks();

    channel = Echo.channel('task-channel')
        .listen('.task.events', (e) => {
            fetchTasks()
            reFetchDataNumber()
        })

});

onUnmounted(() => {
    if (channel) {
        channel.stopListening('.task.events')
    }

})

</script>



<template>
    <div class="flex-1 overflow-x-hidden xl:overflow-x-auto p-2 sm:p-8 bg-background-dark">
        <div class="flex flex-col xl:flex-row h-full gap-6 sm:min-w-[1000px] ">

            <!-- Column: To Do -->
            <div class="flex  flex-col w-1/3 min-w-[90%] sm:min-w-[320px]" @dragover.prevent @drop="dropTask('todo')">

                <div class="flex items-center justify-between mb-4 px-2">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-white uppercase">{{ $t('teamPage.tasks.toDo') }}</h3>
                        <span class="bg-[#283039] text-[#9dabb9] px-2 py-0.5 rounded text-[10px] font-bold">{{
                            todoTasks.length }}</span>
                    </div>
                </div>
                <div
                    class="flex-1 space-y-3 kanban-column p-3 rounded-xl border border-dashed border-slate-800 overflow-y-auto">
                    <button
                        class="w-full flex items-center justify-center py-3 border-2 border-dashed border-border-dark rounded-xl text-[#9dabb9] hover:border-primary hover:text-primary transition-all group mt-2 mb-4 cursor-pointer hover:border-blue-500/50 duration-300 hover:text-blue-500"
                        v-if="userStore.isAdmin" @click="showCreateTask = true">
                        <span
                            class="material-symbols-outlined text-lg mr-2 group-hover:scale-110 transition-transform">add</span>
                        <span class="text-sm font-bold">{{ $t('teamPage.tasks.addNewTask') }}</span>
                    </button>

                    <div v-for="task in todoTasks" :key="task.id" draggable="true" @dragstart="dragStart(task.id)"
                        class="bg-[#1c2229] p-4 rounded-lg shadow-sm border cursor-grab active:cursor-grabbing hover:border-[#147FEC]/50 transition-colors group"
                        :class="{
                            'border-red-500': task.deadline_status === 'danger',
                            'border-yellow-500': task.deadline_status === 'warning',
                            'border-slate-700': task.deadline_status !== 'warning' && task.deadline_status !== 'danger'
                        }">
                        <div class="flex justify-between items-start mb-4"> <span
                                class="text-[10px] font-bold uppercase tracking-wider text-blue-500 bg-blue-500/10 px-2 py-0.5 rounded">
                                {{ $t('teamPage.tasks.toDo') }}</span>
                            <div class="flex items-center gap-1 text-slate-400 text-[10px]"> <span
                                    class="material-symbols-outlined text-sm">schedule</span>
                                <span>{{ task.end_date_formatted || 'Oct 24' }}</span>
                            </div>

                            <!-- <span class="material-symbols-outlined text-slate-400 text-lg opacity-0 group-hover:opacity-100 transition-opacity">drag_indicator</span> -->
                        </div>
                        <div class="flex items-start justify-between">
                            <h4 class="text-sm font-semibold text-white mb-3">{{ task.title }}</h4>
                            <span class="material-symbols-outlined text-sm cursor-pointer" v-if="userStore.isAdmin"
                                @click="handleEditTask(task.id)">edit_note</span>
                        </div>
                    </div>

                </div>
            </div>


            <!-- Teleport modal -->
            <CreateTask v-model:show="showCreateTask" :teamId @re-fetch-tasks="fetchTasks" />
            <UpdateTask v-model:show="showUpdateTask" :teamId @re-fetch-tasks="fetchTasks" v-if="updateTaskId"
                :task-id="updateTaskId" />

            <!-- Column: In Progress -->
            <div class="flex flex-col w-1/3 min-w-[90%] sm:min-w-[320px]" @dragover.prevent
                @drop="dropTask('in_progress')">
                <div class="flex items-center justify-between mb-4 px-2">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-white uppercase">{{
                            $t('teamPage.tasks.inProgress') }}
                        </h3>
                        <span class="bg-[#147FEC]/20 text-[#147FEC] px-2 py-0.5 rounded text-[10px] font-bold"><span>{{
                            inProgressTasks.length }}</span>
                        </span>
                    </div>
                </div>
                <div
                    class="flex-1 space-y-3 kanban-column p-3 rounded-xl border border-dashed border-slate-800 overflow-y-auto">
                    <!-- Active Card -->
                    <div v-for="task in inProgressTasks" :key="task.id" draggable="true" @dragstart="dragStart(task.id)"
                        class="bg-[#1c2229] p-4 rounded-lg shadow-sm border-l-4  border-slate-700 cursor-grab active:cursor-grabbing"
                        :class="{
                            'border-l-red-500': task.deadline_status === 'danger',
                            'border-l-yellow-500': task.deadline_status === 'warning',
                            'border-l-[#147FEC]/50': task.deadline_status !== 'warning' && task.deadline_status !== 'danger'
                        }">
                        <div class="flex justify-between items-start mb-4">
                            <span
                                class="text-[10px] font-bold uppercase  text-[#147FEC] bg-[#147FEC]/10 px-2 py-0.5 rounded">{{
                                    $t('teamPage.tasks.inProgress') }}</span>
                            <span class="material-symbols-outlined text-slate-400 text-lg">drag_indicator</span>
                        </div>
                        <h4 class="text-sm font-semibold text-white mb-4">{{ task.title }}</h4>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">

                                <img class="size-6 rounded-full ring-2 ring-[#1c2229] bg-cover bg-center"
                                    :src="`http://localhost:8000/storage/${task.assigned_to.avatar}`"
                                    :alt="task.assigned_to.name + ' avatar'">

                                <span class="text-[14px] font-semibold text-blue-500 ">{{ task.assigned_to.name
                                }}</span>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <!-- Column: Completed -->
            <div class="flex flex-col w-1/3 min-w-[90%] sm:min-w-[320px]" @dragover.prevent @drop="dropTask('done')">
                <div class="flex items-center justify-between mb-4 px-2">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-white uppercase">
                            {{ $t('teamPage.tasks.completed') }}</h3>
                        <span
                            class="bg-emerald-500/20 text-emerald-500 px-2 py-0.5 rounded text-[10px] font-bold"><span>{{
                                doneTasks.length }}</span>
                        </span>
                    </div>
                </div>
                <div
                    class="flex-1 space-y-3 kanban-column p-3 rounded-xl border border-dashed border-slate-800 overflow-y-auto opacity-80">
                    <!-- Card Done 1 -->

                    <div v-for="task in doneTasks" :key="task.id" draggable="true" @dragstart="dragStart(task.id)"
                        class="bg-[#1c2229]/50 p-4 rounded-lg border border-slate-800 cursor-default">
                        <div class="flex justify-between items-start mb-2">
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-400/10 px-2 py-0.5 rounded">{{
                                    $t('teamPage.tasks.completed') }}</span>
                            <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-500 line-through">
                            {{ task.title }}</h4>
                        <div class="mt-4 flex justify-between">
                            <img class="size-6 rounded-full bg-cover bg-center grayscale"
                                :src="`http://localhost:8000/storage/${task.assigned_to.avatar}`"
                                :alt="task.assigned_to.name + ' avatar'">
                            <button class="text-red-500 text-sm cursor-pointer" @click="handleDeleteTask(task.id)">
                                {{
                                    $t('teamPage.tasks.deleteTask') }}
                            </button>
                        </div>
                    </div>


                    <!-- Read-only footer message -->
                    <!-- <div class="pt-4 text-center"> -->
                    <!-- <p class="text-[11px] :text-slate-500 font-medium">Read-only
                            permissions for this workspace.</p> -->
                    <!-- <p class="text-[10px] text-slate-600">Contact Admin to create new
                            tasks.</p> -->
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
</template>