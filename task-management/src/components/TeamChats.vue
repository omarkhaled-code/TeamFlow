<script setup>
import { ref, onMounted, defineAsyncComponent, computed, onUnmounted } from 'vue';
import api from '@/utils/api';
import Echo from '@/echo';

const props = defineProps({
    teamId: {
        type: Number,
        required: true
    },
})
const emit = defineEmits(['change-active'])


const display = ref("global")
const teamMembers = ref([]);
const other_user_id = ref(null);
const other_user_name = ref(null);


// Glboal Chat Method
const globalUnreadMessageCount = ref(0);

const fetchGlobalChatUnReadMessage = async () => {
    const res = await api.get(`/teams/${props.teamId}/global-chat/un-read-messages`)
    globalUnreadMessageCount.value = res.data.unread_count;
}

const fethcMembers = async () => {
    const res = await api.get(`${props.teamId}/members`);
    teamMembers.value = res.data.members
    fetchGlobalChatUnReadMessage()

}



const GlobalChatAsync = defineAsyncComponent(() =>
    import('@/components/Chats/GlobalChat.vue')
)

const PrivateChatAsync = defineAsyncComponent(() =>
    import('@/components/Chats/PrivateChat.vue')
)

const activeComponent = computed(() => {
    if (display.value === 'global') return GlobalChatAsync
    if (display.value === 'private') return PrivateChatAsync
    return null
})

const handleGoToChat = ({ id, name }) => {
    if (id !== 0) {
        display.value = 'private'
        other_user_name.value = name
    } else {
        display.value = 'global'
    }
    other_user_id.value = id
}

const onlineUsers = ref([])
const isOnline = (userId) => {
    return onlineUsers.value.some(u => u.id === userId)
}


let teamChannel = null;

onMounted(() => {

    fethcMembers()

    teamChannel = Echo.private(`chat.${props.teamId}`)
        .listen('.message.created', async (e) => {
            await fethcMembers()
        })
        .error(err => console.error('Error on Echo channel:', err));

    Echo.join(`team.${props.teamId}`)
        .here((users) => {
            onlineUsers.value = users;
        })
        .joining((user) => {
            if (!onlineUsers.value.some(u => u.id === user.id)) {
                onlineUsers.value.push(user);
            }
        })
        .leaving((user) => {
            onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
        });


})


// 🧹 Cleanup on unmount
onUnmounted(() => {
    if (teamChannel) {
        teamChannel.stopListening('.message.created');
    }

    Echo.leave(`team.${props.teamId}`);
});
</script>

<template>
    <div class="flex flex-1 overflow-hidden">
        <!-- Side Messages display members -->

        <aside class="w-75 flex flex-col bg-[#111418] border-r border-white/5 h-[85vh] rounded-t-md">
            <div class="p-6">
                <h3 class="text-[11px] font-bold uppercase tracking-[1.5px] text-[#5a6b7a] mb-4">
                    {{ $t('teamPage.chats.channels') }}</h3>
                <div class="space-y-1">

                    <div class="w-full flex justify-between items-center px-3 py-3 cursor-pointer transition-all duration-200"
                        :class="display === 'global' ? 'bg-[#137fec]/10 border border-[#137fec]/20 rounded-x' : 'rounded-xl bg-white/1 hover:bg-white/5 border border-white/5 group hover:border-white/10'"
                        @click="handleGoToChat({ id: 0, name: '' })">

                        <div class="flex items-center gap-3 rounded-x">
                            <div
                                class="w-8 h-8 rounded-lg bg-[#137fec]/20 flex items-center justify-center text-[#137fec] group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-lg">public</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Global Room</p>
                                <!-- <p class="text-[10px] text-[#9dabb9]">24 members online</p> -->
                            </div>
                        </div>
                        <span v-if="globalUnreadMessageCount > 0"
                            class="badge text-white text-sm bg-[#137fec] w-[20px] h-[20px] rounded-[50%] flex justify-center items-center">
                            {{ globalUnreadMessageCount }}
                        </span>

                    </div>

                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-6 pb-6">
                <!-- Direct Message -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[11px] font-bold uppercase tracking-[1.5px] text-[#5a6b7a]">
                        {{ $t('teamPage.chats.directMessages') }}</h3>
                </div>
                <!-- Memmbers -->
                <div class="space-y-1" v-for="member in teamMembers" :key="member.id">
                    <div class="flex items-center gap-3 px-3 py-3 rounded-x cursor-pointer"
                        :class="other_user_id === member.id ? 'bg-[#137fec]/10 border border-[#137fec]/20 rounded-x' : 'hover:bg-white/5 group rounded-xl'"
                        @click="handleGoToChat({ id: member.id, name: member.name })">
                        <div class="relative shrink-0">


                            <img class="w-10 h-10 rounded-full bg-center bg-cover"
                                :src="`http://localhost:8000/storage/${member.avatar}`" alt="member avatar"
                                v-if="member.avatar">

                            <img class="w-10 h-10 rounded-full bg-center bg-cover"
                                src="http://localhost:8000/storage/avatars/default.jpg" alt="member avatar" v-else>


                            <!-- small ball that display if user is onlin! -->
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-[#111418] status-glow"
                                v-if="isOnline(member.id)">
                            </div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-zinc-600 rounded-full border-2 border-[#111418]"
                                v-else>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0" v-if="isOnline(member.id)">
                            <p class="text-sm font-bold text-white truncate">{{ member.name }}</p>
                            <p class="text-[11px] text-[#137fec] font-medium truncate">
                                Online
                            </p>
                        </div>
                        <div class="flex-1 min-w-0" v-else>
                            <p class="text-sm font-medium text-[#9dabb9] group-hover:text-white transition-colors
                                truncate">
                                {{ member.name }}</p>
                            <p class="text-[11px] text-[#5a6b7a] truncate">Offline</p>
                        </div>
                        <span v-if="member.unread_count > 0"
                            class="badge text-white text-sm bg-[#137fec] w-[20px] h-[20px] rounded-[50%] flex justify-center items-center">
                            {{ member.unread_count }}
                        </span>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Messge Box -->
        <!-- <GlobalChat v-if="display === 'global'" /> -->
        <component :is="activeComponent" :teamId="props.teamId" :other_user_id :other_user_name
            @re-fetch-members="fethcMembers">
        </component>

    </div>
    <!-- <h1>Team Chat</h1>-->
</template>

<style type="text/tailwindcss">
::-webkit-scrollbar {
    width: 5px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #2d333b;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #3b4754;
}
</style>
