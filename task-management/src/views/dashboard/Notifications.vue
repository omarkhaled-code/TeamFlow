<script setup>
import { useUserStore } from '@/stores/user';

import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
// import api from '@/utils/api';
import NavBar from '@/components/NavBar.vue';
import api from '@/utils/api';
import Echo from '@/echo';
import EmptyJoinRequestCard from '@/components/EmptyJoinRequestCard.vue';

const active = ref('un-read');

const notifications = ref([]);

const fetchNotificatoins = async () => {
    try {
        const { data } = await api.get('notifications');
        notifications.value = data.notifications
        // console.log(notifications.value);

    } catch (error) {
        console.error(error);
    }
}

const getnotifications = computed(() => {
    if (active.value === 'all') {
        return notifications.value
    }
    // return notifications.value
    return notifications.value.filter((n) => n.read_at === null)

})

const getnotificationsCount = computed(() => {
    return notifications.value.length
})

const getUnReadNotificationsCount = computed(() => {
    return notifications.value.filter((n) => n.read_at === null).length
})


const handleMarkRead = async (id) => {
    await api.post(`/notifications/${id}/mark-as-read`)
    fetchNotificatoins()
}

const handleMarkAllRead = async () => {
    const check = confirm("are you sure want to mark all notifications as read!")
    if (!check) return;

    await api.post('/notifications/mark-all-as-read')
    fetchNotificatoins()
}
let requestChannel, memberChannel, teamChannel, taskChannel


onMounted(() => {
    fetchNotificatoins()

    requestChannel = Echo.channel('request-channel')
        .listen('.request.event', (e) => {
            fetchNotificatoins()
        })

    memberChannel = Echo.channel('member-channel')
        .listen('.member.events', (e) => {
            fetchNotificatoins()
        })

    teamChannel = Echo.channel('team-channel')
        .listen('.team.events', (e) => {
            fetchNotificatoins()
        })

    taskChannel = Echo.channel('task-channel')
        .listen('.task.events', (e) => {
            fetchNotificatoins()
        })
})

onBeforeUnmount(() => {
    if (requestChannel) requestChannel.stopListening('.request.event')
    if (memberChannel) memberChannel.stopListening('.member.events')
    if (teamChannel) teamChannel.stopListening('.team.events')
    if (taskChannel) taskChannel.stopListening('.task.events')
})
</script>
<template>
    <div>
        <NavBar :title="$t('nav.notificationsTitle')" :description="$t('nav.notificationsDec')" />
        <section class="flex-1 overflow-y-auto px-6 md:px-12 py-8">

            <!-- Tabs Navigation -->
            <div class="mb-[-1rem]">
                <div class="flex border-b border-slate-800 gap-8">
                    <button
                        class="flex items-center gap-2 border-b-2 border-transparent pb-4 pt-2 transition-all cursor-pointer duration-100"
                        :class="active === 'un-read' && 'active-link'" @click="active = 'un-read'">
                        <p class="text-[.65rem] sm:text-sm font-bold leading-normal tracking-wide">
                            {{ $t('notifications.unReadNotifi') }}
                        </p>
                        <span class="bg-[#1E293B] px-2 py-0.5 rounded text-xs font-bold">{{ getUnReadNotificationsCount
                            }}</span>
                    </button>
                    <button
                        class="flex items-center gap-2 border-b-2 border-transparent text-slate-400 hover:text-slate-200 pb-4 pt-2 transition-all cursor-pointer duration-100"
                        :class="active === 'all' && 'active-link'" @click="active = 'all'">
                        <p class="text-[.65rem] sm:text-sm font-bold leading-normal tracking-wide">
                            {{ $t('notifications.allNotifi') }}</p>
                        <span class="bg-slate-800 px-2 py-0.5 rounded text-xs font-bold">{{ getnotificationsCount
                            }}</span>
                    </button>

                </div>
            </div>
        </section>
        <main class="px-4 sm:px-8 py-6">
            <div class="flex justify-end mb-8 mt-[-1rem]"
                v-if="getUnReadNotificationsCount > 0 && active === 'un-read'">
                <button
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-500/90 text-white text-xs sm:text-sm font-normal sm:font-bold rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95 cursor-pointer"
                    @click="handleMarkAllRead">
                    {{ $t('notifications.markAllAsRead') }}
                </button>
            </div>

            <div v-if="getnotificationsCount === 0" class="text-center">
                <p class="text-slate-400 text-xs sm:text-lg">{{ $t('notifications.nullMessages') }}</p>
                <!-- nullMessages -->
            </div>
            <div v-else class="grid gap-4">
                <div v-if="active === 'un-read' && getUnReadNotificationsCount === 0" class="text-center">
                    <p>{{ $t('notifications.upToDate') }}</p>

                </div>

                <div v-for="notification in getnotifications" :key="notification.id"
                    class="p-2 sm:p-4 rounded-lg bg-[#1A232E] border border-[#1E2D3D] hover:border-blue-500 transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">

                            <h3 class="text-white font-semibold">{{ notification.title }}</h3>
                            <p class="text-slate-400 hidden sm:block text-xs font-bold mt-1">{{ notification.message }}
                            </p>
                            <p class="text-slate-500 text-xs mt-2" v-if="notification.created_at_relative">{{
                                notification.created_at_relative }}</p>
                        </div>

                        <span
                            class="px-2 py-1 rounded text-xs bg-blue-600 hover:bg-blue-800 text-white border border-blue-500/30 cursor-pointer shadow-md hover:shadow-blue-500/10  transition-all duration-200"
                            @click="handleMarkRead(notification.id)" v-if="notification.read_at === null">
                            {{ $t("notifications.markAsRead") }}
                        </span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>