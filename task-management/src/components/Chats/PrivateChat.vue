<script setup lang="ts">
import { ref, watch, onUnmounted, nextTick, computed } from 'vue';
import api from '@/utils/api';
import { useUserStore } from '@/stores/user';
import Echo from '@/echo';
import dayjs from 'dayjs';
import { useLanguage } from '@/composable/useLanguage';

const props = defineProps(['teamId', 'other_user_id', 'other_user_name']);

const { locale } = useLanguage()

const userStore = useUserStore();
const messages = ref([]);
const chatId = ref(null);
const newMessage = ref('');
const chatBox = ref(null);
let chatChannel = null;
// Emit
const emit = defineEmits(['re-fetch-members'])

const ontherUser = ref({
    id: props.other_user_id,
    name: props.other_user_name,
    isTyping: false,
    isTypingTimer: null
})

// 🚀 Helper: scroll to bottom
const scrollToBottom = async () => {
    await nextTick();
    if (chatBox.value) {
        chatBox.value.scrollTo({
            top: chatBox.value.scrollHeight,
            behavior: 'smooth',
        });
    }
};

// ✉️ Send typing event
const sendTypingEvent = () => {
    if (!chatId.value) return;

    Echo.private(`chat.${chatId.value}`)
        .whisper("typing", { userID: userStore.userID });
}

// 🔄 Mark messages as read
const markMessageAsRead = async () => {

    try {
        if (!chatId.value) return;
        await api.post(`/chats/${chatId.value}/mark-as-read`);
        emit("re-fetch-members")
    } catch (err) {
        console.error('Failed to mark messages as read', err);
    }

}

// 🚀 Fetch private chat
const fetchPrivateChat = async () => {
    if (!props.other_user_id) return;

    const res = await api.post(`/teams/${props.teamId}/private-chat`, {
        other_user_id: props.other_user_id,
    });

    messages.value = res.data.messages;
    chatId.value = res.data.chat.id;

    subscribeToChannel(chatId.value);

    scrollToBottom();

    // بعد تحميل الرسائل، اعمل mark as read
    await markMessageAsRead();

};

// 🔔 Subscribe to Echo channel
const subscribeToChannel = (id) => {
    if (chatChannel) chatChannel.stopListening('.message.sent');

    chatChannel = Echo.private(`chat.${id}`)
        .listen('.message.sent', async (e) => {
            messages.value.push(e.message);
            scrollToBottom();
            await markMessageAsRead()


        })
        .listenForWhisper("typing", (res) => {
            ontherUser.value.isTyping = res.userID === props.other_user_id;
            if (ontherUser.value.isTypingTimer) clearTimeout(ontherUser.value.isTypingTimer);
            ontherUser.value.isTypingTimer = setTimeout(() => ontherUser.value.isTyping = false, 600);
        })
        .error(err => console.error('Error on Echo channel:', err));
};

// ✉️ Send message
const sendMessage = async () => {
    if (!newMessage.value.trim() || !chatId.value) return;

    await api.post(`/teams/${chatId.value}/message`, {
        content: newMessage.value,
    });

    newMessage.value = '';
    scrollToBottom();
    markMessageAsRead();
    // ⬅️ السطر المهم
    emit('re-fetch-members');

};

// 🔄 Watch for changes in other_user_id
watch(
    () => props.other_user_id,
    async (newId) => {
        if (!newId) return;
        await fetchPrivateChat();

    },
    { immediate: true }
);

// Get User Avatar
const getUserAvatar = computed(() => {
    let userMessages = messages.value.filter((m) => m.user.id !== userStore.userID)

    return userMessages.length > 0 ? userMessages[0].user.avatar : null
})

// 🧹 Cleanup on unmount
onUnmounted(() => {
    if (chatChannel) chatChannel.stopListening('.message.sent');
});

// ⏰ Format date helper
const formatTime = (dateString) => {
    if (!dateString) return '';
    const actualDate = typeof dateString === 'object' && dateString.date ? dateString.date : dateString;
    const date = dayjs(actualDate);
    return date.isValid() ? date.format('h:mm A') : '...';
};
</script>

<template>
    <main
        class="flex-1 flex flex-col bg-[#090b0e] relative h-[85vh] touch-auto overflow-auto rounded-e-2xl chat-container">

        <!-- Messages Box Header -->
        <header
            class="h-16 border-b border-white/5 flex items-center justify-between px-8 bg-[#111418]/50 backdrop-blur-xl z-10">
            <div class="flex items-center gap-4">
                <img class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                    :src="`http://localhost:8000/storage/${getUserAvatar}`" alt="" v-if="getUserAvatar">
                <img class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                    src="http://localhost:8000/storage/avatars/default.jpg" alt="" v-else>
                <div>
                    <p class="text-sm font-bold text-white">{{ props.other_user_name }}</p>

                    <p class="text-[10px] text-[#9dabb9]">Private Room</p>
                </div>
            </div>

        </header>

        <!-- Messages Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8" ref="chatBox">

            <div v-for="message in messages" :key="message.id">
                <div class="flex gap-4 max-w-4xl ml-auto flex-row-reverse" v-if="message.user.id === userStore.userID">

                    <img class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        :src="`http://localhost:8000/storage/${message.user.avatar}`" alt="" v-if="message.user.avatar">
                    <img class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        src="http://localhost:8000/storage/avatars/default.jpg" alt="" v-else>

                    <div class="space-y-1.5 items-end flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-black">{{ formatTime(message.created_at) }}</span>
                        </div>
                        <div
                            class="bg-[#137fec] px-4 py-3 rounded-2xl rounded-tr-none text-sm leading-relaxed text-white shadow-lg shadow-[#137fec]/10 w-fit break-all">
                            {{ message.content }}
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 max-w-4xl group" v-else>
                    <img class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        :src="`http://localhost:8000/storage/${message.user.avatar}`" alt="" v-if="message.user.avatar">
                    <img class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        src="http://localhost:8000/storage/avatars/default.jpg" alt="" v-else>

                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-black">{{ message.user.name }}</span>
                            <span class="text-[10px] text-[#5a6b7a]">{{ formatTime(message.created_at) }}</span>
                        </div>
                        <div
                            class="bg-[#ffff] text-black border border-white/5 px-4 py-3 rounded-2xl rounded-tl-none text-sm leading-relaxed text-[#d1d5db] shadow-sm w-fit break-all">
                            {{ message.content }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div class="text-[11px] text-[#5a6b7a] mb-2.5 pl-4 flex items-center gap-2" v-if="ontherUser.isTyping">
            <span class="w-1.5 h-1.5 rounded-full bg-[#137fec] animate-pulse"></span>
            <span class="font-bold text-[#137fec]">{{ props.other_user_name }}</span> {{ $t('teamPage.chats.isTyping')
            }}
        </div>

        <!-- Message Input -->
        <div class="px-4 pb-4 bg-gradient-from-[#0b0f14] via-[#0b0f14] to-transparent pt-4 bg-[#111418]">
            <div
                class="glass-panel rounded-2xl flex items-center p-2 focus-within:ring-2 focus-within:ring-[#137fec]/40 transition-all shadow-xl">
                <input
                    class="flex-1 bg-transparent border-none text-sm text-white placeholder:text-[#5a6b7a] focus:ring-0 px-2 outline-none"
                    @keydown="sendTypingEvent" v-model="newMessage" placeholder="Write a message..." type="text"
                    @keyup.enter="sendMessage" />
                <div class="flex items-center gap-1.5 pr-1">
                    <button
                        class="p-2.5 px-3 bg-[#137fec] text-white rounded-xl hover:bg-[#137fec]/90 transition-all ml-2 shadow-lg shadow-[#137fec]/30 active:scale-95 cursor-pointer"
                        @click="sendMessage">
                        <span class="material-symbols-outlined" :class="locale === 'ar' && 'rotate-180'">send</span>
                    </button>
                </div>
            </div>
        </div>
    </main>
</template>

<style>
.chat-container {
    background-image: url('../../assets/chat-background-3.png');
    background-repeat: repeat;
    background-size: auto;
    background-position: center;
}
</style>
