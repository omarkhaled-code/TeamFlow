<script setup>
import api from "@/utils/api";
import { defineProps, defineEmits, ref, nextTick, watchEffect } from "vue";

const props = defineProps({
    show: Boolean,
    teamId: Number
});

const emit = defineEmits(["update:show", 're-fetch-tasks']);

const close = () => emit("update:show", false);

const dateInput = ref(null);
const newTasInput = ref(null);

const reFetchTasks = () => emit('re-fetch-tasks')

const newTask = ref({
    title: "",
    end_date: ""
})

const openDatePicker = async () => {
    // ننتظر التأكد إن الـ input موجود في DOM
    await nextTick();

    if (dateInput.value?.showPicker) {
        dateInput.value.showPicker(); // Chrome / Edge
    } else {
        dateInput.value?.click(); // fallback
    }
};

const handleDate = (event) => {
    newTask.value.end_date = event.target.value;


};
watchEffect(() => {
    if (props.show) {
        newTasInput.value && newTasInput.value.focus();
    }
}, [])

const handleAddTask = async () => {
    await api.post(`/teams/${props.teamId}/tasks`, { title: newTask.value.title, end_date: newTask.value.end_date })
    reFetchTasks()
    close()
    newTask.value.title = ''
    newTask.value.end_date = ''
}
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-6">
        <!-- overlay -->
        <div class="absolute inset-0 bg-background-dark/80 backdrop-blur-2xl" @click.self="close"></div>

        <!-- modal -->
        <div
            class="relative w-full max-w-lg bg-surface-dark border border-white/10 rounded-2xl shadow-[0_32px_64px_-16px_rgba(0,0,0,0.6)] overflow-hidden">
            <div class="p-8">
                <div class="flex flex-col gap-8">

                    <!-- title -->
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-500">
                            New Task Entry
                        </label>
                        <input
                            class="w-full bg-transparent border-none p-0 text-2xl font-semibold text-white placeholder:text-white/20 focus:ring-0 outline-none"
                            placeholder="What needs to be done?" type="text" ref="newTasInput"
                            v-model="newTask.title" />
                    </div>

                    <!-- DATE PICKER -->
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 bg-white/5 border border-white/10 hover:border-blue-500/50 px-8 py-3 rounded-xl transition-all cursor-pointer"
                            @click="openDatePicker">
                            <span class="material-symbols-outlined text-blue-500">
                                calendar_month
                            </span>

                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    End Date
                                </span>
                                <span class="text-sm text-white font-medium">
                                    {{ newTask.end_date || 'Select date...' }}
                                </span>
                            </div>

                            <!-- input مخفي تمامًا -->
                            <input ref="dateInput" type="date" class="absolute opacity-0 pointer-events-none"
                                @change="handleDate" />
                        </div>
                        <div class="flex-1"></div>
                    </div>

                    <!-- buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button
                            class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-white transition-colors cursor-pointer"
                            @click="close">
                            Cancel
                        </button>
                        <button
                            class="px-8 py-3 bg-blue-500 hover:bg-blue-500/90 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95 cursor-pointer"
                            @click="handleAddTask">
                            Add Task
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
