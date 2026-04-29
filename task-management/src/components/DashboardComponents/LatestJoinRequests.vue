<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import api from '@/utils/api';
import Echo from '@/echo';
import EmptyJoinRequestCard from '../EmptyJoinRequestCard.vue';

const requests = ref([])
const getUserSumarizeRequests = async () => {
    const { data } = await api.get('/dashboard/join-requests');
    requests.value = data.data;
}

const approveJionRequest = async (joinRequestId) => {
    try {
        await api.post(`/join-requests/approve`, { request_id: joinRequestId })
        getUserSumarizeRequests()
    }
    catch (error) {
        console.error(error);
    }

}

const rejectJionRequest = async (joinRequestId) => {
    try {
        await api.post(`/join-requests/reject`, { request_id: joinRequestId })

        getUserSumarizeRequests()
    }
    catch (error) {
        console.error(error);
    }

}

let channel;

onMounted(() => {
    getUserSumarizeRequests();

    channel = Echo.channel('request-channel')
        .listen('.request.event', (e) => {
            getUserSumarizeRequests()
        })

});

onUnmounted(() => {
    if (channel) {
        channel.stopListening('.request.event')

    }

})
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold">{{ $t('dashboard.joinRequestSumarizeTitle') }}</h3>
            <span class="bg-blue-500 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">{{ requests.length }}
                New</span>

        </div>
        <div class="space-y-4">

            <div class="bg-[#1a232e] border border-[#283039] rounded-xl p-4" v-for="request in requests"
                v-if="requests.length > 0" :key="request.id">
                <div class="flex gap-3 mb-4">
                    <img class="h-10 w-10 rounded-full bg-cover"
                        :src="`http://localhost:8000/storage/${request.user.avatar}`" alt="Avatar of team recruiter"
                        v-if="request.user.avatar">

                    <div class="flex-1">
                        <p class="text-sm">
                            <span class="font-bold">{{ request.user.name }}</span> wants to join <span
                                class="text-blue-500 font-bold">{{ request.team.name }}</span>
                        </p>
                        <p class="text-[11px] text-[#9dabb9] mt-0.5">{{ request.created_at_human }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button
                        class="flex-1 py-1.5 bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer"
                        @click="approveJionRequest(request.id)">Accept</button>
                    <button
                        class="flex-1 py-1.5 bg-[#283039] hover:bg-red-500/20 hover:text-red-400 text-[#9dabb9] text-xs font-bold rounded-lg transition-colors cursor-pointer"
                        @click="rejectJionRequest(request.id)">Decline</button>
                </div>
            </div>
            <EmptyJoinRequestCard v-else />

        </div>
    </div>
</template>