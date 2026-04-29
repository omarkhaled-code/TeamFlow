<script setup>
import api from "@/utils/api";
import { defineProps, defineEmits, ref, nextTick, watchEffect } from "vue";

const props = defineProps({
    show: Boolean,
    teamId: Number
});

const emit = defineEmits(["showJoinRequest:show", 're-fetch-tasks']);

const close = () => emit("showJoinRequest", false);

const newTasInput = ref(null);

const reFetchTasks = () => emit('re-fetch-tasks')

const newTask = ref({
    title: "Register API",
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
                        <input
                            class="w-full bg-transparent border-none p-0 text-2xl font-semibold text-white placeholder:text-white/20 focus:ring-0 outline-none"
                            placeholder="What needs to be done?" type="text" ref="newTasInput"
                            v-model="newTask.title" />
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
                            Send Join Request
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
