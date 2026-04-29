<script setup>
import { useUserStore } from '@/stores/user';
import api from '@/utils/api';
import { onMounted, onUnmounted, ref } from 'vue';
import Echo from '@/echo';

const props = defineProps({
    teamId: {
        type: Number,
        required: true
    },
})


const userStore = useUserStore()
const members = ref([]);

// Emit
const emit = defineEmits('re-fetch-data-numbers')
const reFetchDataNumber = () => emit("re-fetch-data-numbers");

const getTeamMember = async () => {
    const { data } = await api.get(`teams/${props.teamId}/members`)
    members.value = data.data
}
const defaultAvatar = 'https://lh3.googleusercontent.com/aida-public/AB6AXuARqcI_8ROAelMrCK5oVEAU3-fOp9SkT5Dkm7RD3HqqSSWAqXC-xuzrZHJ54efVRGYrPY-1WtmW8x5VtpR6zK7PyMXJ0t25WbwrIyzZo3BrBuc7QSfKDcCETW13K6JfeafI0mn001q9nw-Kj6POUNWOK57pvkrZmxWLAi9HIyfOEETAGvo9DvphvpPU-MigicZk3B65XpwILY_lNTGrAQ4nLrsWsjMw1C3PKNIgmQBiYm5PDoeGPS_x8JPjNhO3jpHCgXV4a7vd7tI'
const defaultDate = 'Oct 23, 2025'
const defaultRole = 'member'


const handleDeleteMember = async (id) => {
    const check = confirm("are you sure want to delet member from team!")
    if (!check) return;
    // delete member logic
    try {
        await api.post(`/teams/${props.teamId}/delete-user`, { user_id: id })
    } catch (error) {
        console.error(error);
    }
    //reFetch Data Number
    reFetchDataNumber()
    getTeamMember()
}

let channel;

onMounted(() => {
    getTeamMember();

    channel = Echo.channel('member-channel')
        .listen('.member.events', (e) => {
            getTeamMember()
            reFetchDataNumber()
        })

});

onUnmounted(() => {
    if (channel) {
        channel.stopListening('.member.events')
    }

})



</script>
<template>
    <div class="bg-slate-900 rounded-xl border border-slate-800 shadow-sm overflow-hidden">
        <div class="sm:overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-slate-800">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">
                            {{ $t('teamPage.members.members') }}</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider hidden sm:table-cell">
                            {{ $t('teamPage.members.role') }}</th>

                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">
                            {{ $t('teamPage.members.joined') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right hidden lg:table-cell"
                            v-if="userStore.isAdmin">
                            {{ $t('teamPage.members.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <!-- Rows -->
                    <tr class="hover:bg-slate-800/30 transition-colors" v-for="member in members" :key="member.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img class="size-10 rounded-full bg-cover bg-center"
                                    :src="`http://localhost:8000/storage/${member.avatar}`"
                                    :alt="member.name + ' headshot'">
                                <div>
                                    <p class="text-sm font-bold text-white">{{ member.name }}</p>
                                    <p class="text-xs text-slate-500">{{ member.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <div class="max-w-[120px] text-slate-400 hidden sm:inline-block">
                                {{ member.pivot.role }}
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-400 font-medium hidden lg:table-cell">{{ defaultDate }}
                        </td>
                        <td class="px-6 py-4 text-right hidden lg:table-cell"
                            v-if="member.pivot.role !== 'admin' && userStore.isAdmin">
                            <button
                                class="p-2 text-slate-400  rounded-lg transition-colors cursor-pointer hover:text-red-700 scale:-100 hover:scale-105 duration-150"
                                @click="handleDeleteMember(member.id)">
                                <span class="material-symbols-outlined">
                                    person_cancel
                                </span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>