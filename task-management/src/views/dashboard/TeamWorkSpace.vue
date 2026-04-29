<script setup>
import NavBar from '@/components/NavBar.vue';
import { useUserStore } from '@/stores/user';
import api from '@/utils/api';
import { ref, defineAsyncComponent, computed, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';


const route = useRoute()
const router = useRouter()

const userStore = useUserStore()
const { getUserRole, isAdmin } = useUserStore()
const teamId = ref(route.params.id)
const teamJoinCode = ref(null)
const active = ref(localStorage.getItem('currentComponent') || 'tasks');
const teamNumbersData = reactive({ memberCount: 0, joinRequestsCount: 0, tasksCount: 0 })
const displayChat = ref(false);

const getTeamNumbersData = async () => {

    const { data } = await api.get(`/teams/${teamId.value}/teamNumbersData`);

    if (userStore.isAdmin) {
        teamNumbersData.joinRequestsCount = data.data.join_requests_count;
        teamJoinCode.value = data.data.team_join_code
        console.log(teamJoinCode.value);

    }
    teamNumbersData.memberCount = data.data.member_count;
    teamNumbersData.tasksCount = data.data.tasks_count



}


const handleCopyJoinCode = async () => {
    try {
        await navigator.clipboard.writeText(teamJoinCode.value)
        alert("Copied!")
    } catch (err) {
        console.error("Copy failed:", err)
    }
}

const activeAdminComponent = computed(() => {
    if (active.value === 'members') {
        return defineAsyncComponent(() => import('@/components/MemberTable.vue'));
    } else if (active.value === 'tasks') {
        return defineAsyncComponent(() => import('@/components/Tasks.vue'));
    } else if (active.value === 'join-requests') {
        return defineAsyncComponent(() => import('@/components/JoinRequestsTable.vue'));
    } else if (active.value === 'team-chats' && displayChat) {
        return defineAsyncComponent(() => import('@/components/TeamChats.vue'));
    }

    return null;
});
// 3|8HqfWrg7nxHWelG3F7B1sqf8VTneQT90n4fxisZT18ff46ce

const activeMemberComponent = computed(() => {
    if (active.value === 'members') {
        return defineAsyncComponent(() => import('@/components/MemberTable.vue'));
    } else if (active.value === 'tasks') {
        return defineAsyncComponent(() => import('@/components/Tasks.vue'));
    } else if (active.value === 'team-chats' && displayChat) {
        return defineAsyncComponent(() => import('@/components/TeamChats.vue'));
    }
    return null;
});

const checkTeamChat = async () => {
    const res = await api.post("/check-team-chat", { team_id: teamId.value })

    displayChat.value = res.data.allowed
}

const handleUpdateTeam = () => {
    router.push({ name: 'update-team', params: { id: teamId.value } })
}

onMounted(() => {
    getUserRole(teamId.value)
    // console.log(isAdmin);

    getTeamNumbersData()
    checkTeamChat()

})


const changeComponent = (newComponent) => {
    active.value = newComponent
    localStorage.setItem("currentComponent", newComponent)
}


const chagneActive = () => {
    if (displayChat.value) return;
    // active.value = 'tasks'
    // localStorage.setItem("currentComponent", 'tasks')
}
</script>

<template>
    <!-- <NavBar :title="`Team Workspace`"
        :description="`Manage your team's projects, tasks, and collaboration in one place.`" /> -->
    <section class="flex-1 overflow-y-auto px-3 sm:px-6 md:px-12 py-8">

        <!-- Tabs Navigation -->
        <div
            class="flex flex-col lg:flex-row border-b border-slate-800 w-full justify-between lg:items-center mb-8 lg:mb-6">
            <div class="flex flex-col flex-wrap lg:flex-row mb-8 lg:mb-4 gap-8">
                <button
                    class="flex items-center gap-2 border-b-2 border-transparent pb-4 pt-2 transition-all cursor-pointer duration-100"
                    :class="active === 'members' && 'active-link'" @click="changeComponent('members')">
                    <p class="text-sm font-bold leading-normal tracking-wide uppercase">{{ $t('teamPage.members.title')
                    }}
                    </p>

                </button>
                <button
                    class="flex items-center gap-2 border-b-2 border-transparent text-slate-400 hover:text-slate-200 pb-4 pt-2 transition-all cursor-pointer duration-100"
                    :class="active === 'join-requests' && 'active-link'" @click="changeComponent('join-requests')"
                    v-if="userStore.isAdmin">
                    <p class="text-sm font-bold leading-normal tracking-wide uppercase">
                        {{ $t('teamPage.requests.title') }}</p>

                </button>
                <button
                    class="flex items-center gap-2 border-b-2 border-transparent text-slate-400 hover:text-slate-200 pb-4 pt-2 transition-all cursor-pointer duration-100"
                    :class="active === 'tasks' && 'active-link'" @click="changeComponent('tasks')">
                    <p class="text-sm font-bold leading-normal tracking-wide uppercase">{{ $t('teamPage.tasks.title')
                    }}</p>

                </button>
                <button
                    class="flex items-center gap-2 border-b-2 border-transparent text-slate-400 hover:text-slate-200 pb-4 pt-2 transition-all cursor-pointer duration-100"
                    :class="active === 'team-chats' && 'active-link'" @click="changeComponent('team-chats')"
                    v-if="displayChat">
                    <p class="text-sm font-bold leading-normal tracking-wide uppercase">{{ $t('teamPage.chats.title') }}
                    </p>

                </button>
            </div>
            <div v-if="userStore.isAdmin" class="flex flex-col lg:flex-row gap-8 mb-8 lg:mb-4">
                <span class="material-symbols-outlined text-sm cursor-pointer hover:text-blue-500"
                    v-if="userStore.isAdmin" @click="handleUpdateTeam">edit_note</span>
                <!-- <button @click="handleUpdateTeam" class="hover:text-blue-500 duration-200 cursor-pointer">{{
                    $t('teamPage.update') }}</button> -->

                <p class="text-sm font-bold leading-normal tracking-wide uppercase text-slate-400 hover:text-slate-200 cursor-pointer"
                    @click="handleCopyJoinCode">
                    {{ $t('teamPage.code') }} : <span class="text-blue-500 underline cursor-default">{{ teamJoinCode
                    }}</span></p>
            </div>

        </div>
        <!-- Members Table -->

        <component :is="activeAdminComponent" :teamId v-if="userStore.isAdmin"
            @re-fetch-data-numbers="getTeamNumbersData(isAdmin && 'admin')" @change-active="chagneActive"></component>

        <component :is="activeMemberComponent" :teamId v-else
            @re-fetch-data-numbers="getTeamNumbersData(isAdmin && 'admin')" @change-active="chagneActive"></component>

    </section>
</template>