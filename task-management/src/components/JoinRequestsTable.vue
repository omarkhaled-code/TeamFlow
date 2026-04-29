<script setup>
import router from '@/router';
import api from '@/utils/api';
import { onMounted, onUnmounted, ref } from 'vue';
import Echo from '@/echo';
import EmptyJoinRequestCard from './EmptyJoinRequestCard.vue';

const props = defineProps({
    teamId: {
        type: Number,
        required: true
    },
})

const emit = defineEmits('re-fetch-data-numbers')
const reFetchDataNumber = () => emit("re-fetch-data-numbers");

const join_requests = ref([]);
const getJoinRequests = async () => {
    const { data } = await api.get(`teams/${props.teamId}/join-requests`)

    join_requests.value = data.join_requests
}
const approveJionRequest = async (joinRequestId) => {
    try {
        await api.post(`/join-requests/approve`, { request_id: joinRequestId })
        getJoinRequests()
        reFetchDataNumber()
    }
    catch (error) {
        console.error(error);
    }

}

const rejectJionRequest = async (joinRequestId) => {
    try {
        await api.post(`/join-requests/reject`, { request_id: joinRequestId })
        getJoinRequests()
        reFetchDataNumber()
    }
    catch (error) {
        console.error(error);
    }

}

let channel;
onMounted(() => {
    getJoinRequests()

    channel = Echo.channel('request-channel')
        .listen('.request.event', (e) => {
            getUserSumarizeRequests()
            getJoinRequests()
        })
});

onUnmounted(() => {
    if (channel) {
        channel.stopListening('.request.event')
    }

})



</script>
<template>
    <div class="max-w-[1200px] mx-auto p-8" v-if="join_requests.length > 0">

        <!-- Search and Filter Bar -->
        <div class="bg-background-dark border border-[#1C293C] rounded-xl p-4 mb-6 shadow-sm"
            v-if="join_requests.length > 4">
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <div class="relative w-full">
                    <span
                        class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#8FA1B9]">search</span>
                    <input
                        class="w-full bg-[#162034] border-none rounded-lg pl-12 pr-4 py-3 text-white placeholder:text-[#8FA1B9] focus:ring-2  focus:ring-blue-500 transition-shadow  outline-none"
                        placeholder="Search members by name, email..." type="text" />
                </div>

            </div>
        </div>
        <!-- Requests Table/List -->
        <div class="bg-background-dark border border-[#1C293C] rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#162034] border-b border-[#1C293C]">
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#9dabb9] w-1/3">
                                {{ $t('teamPage.requests.member') }}</th>

                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#9dabb9] text-right">
                                {{ $t('teamPage.requests.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-[#0E172B]">
                        <!-- Row 1 -->
                        <tr class="hover:bg-slate-800/30 transition-colors group" v-for="request in join_requests"
                            :key="request.id">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <img class="size-10 rounded-full bg-[#283039] bg-cover bg-center"
                                        :src="`http://localhost:8000/storage/${request.user.avatar}`"
                                        :alt="request.name + ' headshot'">

                                    <div class="flex flex-col">
                                        <span class="font-bold text-white">{{ request.user.name }}</span>
                                        <span class="text-xs text-[#9dabb9]">{{ request.user.email }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5">
                                <div class="flex justify-end gap-3">
                                    <button
                                        class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                                        @click="approveJionRequest(request.id)">
                                        <span class="material-symbols-outlined text-base">check</span>
                                        Approve
                                    </button>
                                    <button
                                        class="px-4 py-2 rounded-lg border border-transparent  text-xs font-bold transition-all bg-red-500 hover:bg-red-800 hover:border-transparent text-white cursor-pointer transition-all duration-200"
                                        @click="rejectJionRequest(request.id)">
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- <div class="max-w-[1200px] mx-auto p-8" v-else>
        <h3 class="text-center text-2xl text-gray-500 underline">No Joining Requests For This Team</h3>
    </div> -->
    <EmptyJoinRequestCard v-else />
</template>