<script setup>

import { useUserStore } from '@/stores/user';
import { useRouter, RouterLink } from 'vue-router';
import api from '@/utils/api';
import { onMounted, onUnmounted, ref, computed } from 'vue';
import { useLanguage } from '@/composable/useLanguage';

import Echo from '@/echo';


const userStore = useUserStore();
const router = useRouter();

const unReadNotifi = ref(0);

const { locale, changeLanguage } = useLanguage()
const isOpen = ref(false)
const dropdownRef = ref(null)


const handleLogout = () => {
    api.post('/logout').catch((error) => {
        console.error('Logout failed:', error.response?.data?.message || error.message);
    });
    userStore.logout();
    router.push('/registration');
}

const handleLang = (lang) => {
    changeLanguage(lang)
    isOpen.value = false
}

const currentLanguageName = computed(() => {
    return locale.value === 'ar' ? 'العربية' : locale.value === 'en' ? 'English' : "Germany"
})

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false
    }
}

const fetchingUnReadNotifications = async () => {
    const { data } = await api.get('/notifications/unread-count')
    unReadNotifi.value = data.unread_count
}

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    }

})

let requestChannel, memberChannel, teamChannel, taskChannel


onMounted(() => {
    fetchingUnReadNotifications()

    requestChannel = Echo.channel('request-channel')
        .listen('.request.event', (e) => {
            fetchingUnReadNotifications()
        })

    memberChannel = Echo.channel('member-channel')
        .listen('.member.events', (e) => {
            fetchingUnReadNotifications()
        })

    teamChannel = Echo.channel('team-channel')
        .listen('.team.events', (e) => {
            fetchingUnReadNotifications()
        })

    taskChannel = Echo.channel('task-channel')
        .listen('.task.events', (e) => {
            fetchingUnReadNotifications()
        })
    document.addEventListener('click', handleClickOutside)

})


onUnmounted(() => {
    if (requestChannel) requestChannel.stopListening('.request.event')
    if (memberChannel) memberChannel.stopListening('.member.events')
    if (teamChannel) teamChannel.stopListening('.team.events')
    if (taskChannel) taskChannel.stopListening('.task.events')

    // 
    document.removeEventListener('click', handleClickOutside)

})

</script>
<template>
    <header
        class="flex items-center justify-end md:justify-between  px-8 py-2.5  backdrop-blur-md sticky top-0 z-10 border-b border-[#1E2D3D] bg-[#0a0f15]">
        <div class="items-center gap-4 hidden md:flex">
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <h2 class="text-white text-md lg:text-lg font-bold leading-tight">{{ props.title }}</h2>

                </div>
                <p class=" text-slate-400 text-[0.65rem] lg:text-xs">{{ props.description }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 ml-4">
            <!-- Language Dropdown -->
            <div class="relative" ref="dropdownRef">
                <button @click="isOpen = !isOpen"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg  hover:bg-[#283039] text-white transition-colors duration-200 cursor-pointer">
                    <span class="material-symbols-outlined text-base">language</span>
                    <span class="text-sm font-medium">{{ currentLanguageName }}</span>
                    <span class="material-symbols-outlined text-base">
                        {{ isOpen ? 'expand_less' : 'expand_more' }}
                    </span>
                </button>

                <!-- Dropdown Menu -->
                <div v-if="isOpen"
                    class="absolute top-full mt-2 right-0 bg-[#1a1f2e] border border-[#30363d] rounded-lg shadow-xl overflow-hidden min-w-[180px] z-50">
                    <button @click="handleLang('en')"
                        class="w-full px-4 py-3 text-left transition-colors flex items-center gap-3 duration-200"
                        :class="locale === 'en' ? 'cursor-default ' : 'cursor-pointer hover:bg-[#0A0F15]'">
                        <span class="text-2xl">🇺🇸</span>
                        <span class="text-sm text-white font-medium">English</span>
                        <span v-if="locale === 'en'"
                            class="material-symbols-outlined text-blue-500 ml-auto text-base">check</span>
                    </button>

                    <button @click="handleLang('ge')"
                        class="w-full px-4 py-3 text-left transition-colors flex items-center gap-3 duration-200"
                        :class="locale === 'ge' ? 'cursor-default ' : 'cursor-pointer hover:bg-[#0A0F15]'">
                        <span class="text-2xl">🇩🇪</span>
                        <span class="text-sm text-white font-medium">Germany</span>
                        <span v-if="locale === 'ge'"
                            class="material-symbols-outlined text-blue-500 ml-auto text-base">check</span>
                    </button>

                    <!-- <button @click="handleLang('ar')"
                        class="w-full px-4 py-3 text-left hover:bg-[#30363d] transition-colors flex items-center gap-3"
                        :class="{ 'bg-[#0A0F15]': locale === 'ar' }"> -->
                    <button @click="handleLang('ar')"
                        class="w-full px-4 py-3 text-left transition-colors flex items-center gap-3 duration-200"
                        :class="locale === 'ar' ? 'cursor-default ' : 'cursor-pointer hover:bg-[#0A0F15]'">
                        <span class="text-2xl">🇸🇦</span>
                        <span class="text-sm text-white font-medium">العربية</span>
                        <span v-if="locale === 'ar'"
                            class="material-symbols-outlined text-blue-500 ml-auto text-base">check</span>
                    </button>
                </div>
            </div>

            <!--  -->

            <router-link :to="{ name: 'notifications' }"
                class="p-2 text-[#9dabb9] hover:text-white rounded-lg hover:bg-[#283039] cursor-pointer">
                <span class="material-symbols-outlined text-red-500" v-if="unReadNotifi > 0">
                    notifications_unread
                </span>
                <span class="material-symbols-outlined" v-else>
                    notifications
                </span>
            </router-link>
            <div class="h-8 w-[1px] bg-[#283039] mx-2"></div>
            <button class="p-2 text-[#9dabb9] hover:text-white rounded-lg hover:bg-red-700 cursor-pointer duration-200"
                @click="handleLogout">
                <span class="material-symbols-outlined">
                    logout
                </span>
            </button>
        </div>
    </header>
</template>