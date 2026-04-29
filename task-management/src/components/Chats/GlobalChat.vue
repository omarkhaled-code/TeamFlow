<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import api from '@/utils/api';
import { useUserStore } from '@/stores/user';
import Echo from '@/echo';
import dayjs from 'dayjs';
import { useLanguage } from '@/composable/useLanguage';

const userStore = useUserStore();

const { locale } = useLanguage()

const messages = ref([]);
const chatID = ref(null); // رقم الشات
const newMessage = ref('');
const teamId = ref(1); // حط هنا الـ teamId الديناميكي
const emit = defineEmits(['re-fetch-members']);

const chatBox = ref(null);

// Listen to typing user!
const typingUsers = ref([]);
const typingUserName = ref("")
let typingTimers = {};
let chatChannel = null;

// ===== جلب الرسائل =====
const fetchGlobalChatMessages = async () => {
    const res = await api.get(`/teams/${teamId.value}/global-chat`);
    messages.value = res.data.chat.messages;
    chatID.value = res.data.chat.id;
};

// ===== Scroll تلقائي =====
const scrollToBottom = async () => {
    await nextTick();
    if (chatBox.value) {
        chatBox.value.scrollTo({
            top: chatBox.value.scrollHeight,
            behavior: 'smooth',
        });
    }
};

// ===== تمييز الرسائل كمقروءة =====
const markMessageAsRead = async () => {
    if (!chatID.value) return;
    try {
        await api.post(`/chats/${chatID.value}/mark-as-read`);
        emit("re-fetch-members");
    } catch (error) {
        console.error('Failed to mark messages as read', error);
    }
};

// ===== إرسال رسالة =====
const sendMessage = async () => {
    if (!newMessage.value.trim()) return;

    await api.post(`/teams/${chatID.value}/message`, {
        content: newMessage.value
    });

    newMessage.value = '';
    scrollToBottom();
};

// ===== تنسيق الوقت =====
const formatTime = (dateString) => {
    if (!dateString) return '';
    const actualDate = typeof dateString === 'object' && dateString.date
        ? dateString.date
        : dateString;

    const date = dayjs(actualDate);
    if (!date.isValid()) return '...';

    return date.format('h:mm A');
};

// Listener Send Event
let typingTimeout = null;

const sendTypingEvent = () => {
    if (!chatID.value) return;

    if (typingTimeout) return;

    Echo.private(`chat.${chatID.value}`)
        .whisper("typing", { userID: userStore.userID, userName: userStore.userName });

    typingTimeout = setTimeout(() => {
        typingTimeout = null;
    }, 300);
};

// ===== التشغيل عند التحميل =====
onMounted(async () => {
    await fetchGlobalChatMessages();
    await markMessageAsRead();
    scrollToBottom();

    // الاشتراك في قناة الشات
    chatChannel = Echo.private(`chat.${chatID.value}`)
        .listen('.message.sent', async (e) => {
            messages.value.push(e.message);
            scrollToBottom();
            await markMessageAsRead();
        })
        .listenForWhisper("typing", (res) => {
            const userId = res.userID;
            typingUserName.value = res.userName

            if (!typingUsers.value.includes(userId)) {
                typingUsers.value.push(userId);
            }

            clearTimeout(typingTimers[userId]);
            typingTimers[userId] = setTimeout(() => {
                typingUsers.value = typingUsers.value.filter(id => id !== userId);
            }, 800);
        })
        .error((error) => console.error('خطأ في الوصول للقناة:', error));
});

// ===== إلغاء الاشتراك عند الخروج =====
onUnmounted(() => {
    chatChannel?.stopListening('.message.sent');
});
</script>

<template>
    <!-- <main class="flex-1 flex flex-col bg-[#0b0f14] relative overflow-hidden "> -->
    <main
        class="flex-1 flex flex-col bg-[#090b0e] relative h-[85vh] touch-auto overflow-auto rounded-e-2xl chat-container">
        <!-- Messages Box Header -->
        <header
            class="h-16 border-b border-white/5 flex items-center justify-between px-8 bg-[#111418]/50 backdrop-blur-xl z-10">
            <div class="flex items-center gap-4">
                <div
                    class="w-8 h-8 rounded-lg bg-[#137fec]/20 flex items-center justify-center text-[#137fec] group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-lg">public</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-white">Global Room</p>
                </div>
            </div>
        </header>
        <!-- Messges Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8 " ref="chatBox">

            <!-- Display Today to user, to tell him messages come date! -->
            <!-- <div class="relative flex items-center justify-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/5"></div>
                </div>
                <span class="relative bg-[#0b0f14] px-6 text-[10px] font-bold text-[#5a6b7a] uppercase tracking-[3px]">
                    Today, October 23
                </span>
            </div> -->

            <div v-for="message in messages">
                <div class="flex gap-4 max-w-4xl ml-auto flex-row-reverse" v-if="message.user.id === userStore.userID">

                    <img class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        :src="`http://localhost:8000/storage/${message.user.avatar}`" alt="" v-if="message.user.avatar">
                    <img class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        src="http://localhost:8000/storage/avatars/default.jpg" alt="" v-else>

                    <div class="space-y-1.5 items-end flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-black">{{ formatTime(message.created_at) }}</span>
                            <!-- <span class="text-[10px] text-[#5a6b7a]"></span> -->
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

            <div></div>
        </div>
        <!-- Messge Box Send Message -->
        <div class="text-[11px] text-[#5a6b7a] mb-2.5 pl-4 flex items-center gap-2" v-if="typingUsers.length">
            <span class="w-1.5 h-1.5 rounded-full bg-[#137fec] animate-pulse"></span>
            <span class="font-bold text-[#137fec]">{{ typingUserName }}</span> {{ $t('teamPage.chats.isTyping') }}
        </div>
        <div class="px-4 pb-4 bg-gradient-from-[#0b0f14] via-[#0b0f14] to-transparent pt-4 bg-[#111418]">
            <div
                class="glass-panel rounded-2xl flex items-center p-2 focus-within:ring-2 focus-within:ring-[#137fec]/40 transition-all shadow-xl">
                <input
                    class="flex-1 bg-transparent border-none text-sm text-white placeholder:text-[#5a6b7a] focus:ring-0 px-2 outline-none"
                    v-model="newMessage" placeholder="Write a message to Alex..." type="text" @input="sendTypingEvent"
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
    /* WhatsApp style */
    background-size: auto;
    /* don’t stretch */
    background-position: center;

    /* height: 100%; */

    /* padding: 16px; */
}
</style>