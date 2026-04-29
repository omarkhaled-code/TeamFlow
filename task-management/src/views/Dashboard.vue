<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useUserStore } from '@/stores/user';
import NavBar from '@/components/NavBar.vue';
import LatestJoinRequests from '@/components/DashboardComponents/LatestJoinRequests.vue';
import MyTeamsSumarize from '@/components/DashboardComponents/MyTeamsSumarize.vue';
import api from '@/utils/api';
import { onMounted, reactive } from 'vue';
const userStore = useUserStore();
const router = useRouter();

const numbersData = reactive({ total_teams: 0, active_tasks: 0, joinRequests: 0 })

const getUserNumbersData = async () => {
    const { data } = await api.get('/user-numbers-data')
    numbersData.total_teams = data.data.total_teams_count
    numbersData.active_tasks = data.data.active_tasks_count
    numbersData.joinRequests = data.data.join_requests_count
}

onMounted(() => {
    getUserNumbersData()

})

</script>

<template>

    <nav-bar :title="$t('nav.dashboardTitle')" :description="$t('nav.dashboardDec')" />
    <div class="p-8 max-w-7xl mx-auto w-full">
        <!-- Page Heading & Stats -->
        <div class="flex flex-col gap-6 mb-8">
            <div>
                <!-- <h2 class="text-3xl font-black tracking-tight mb-1">Team Overview</h2> -->
                <h2 class="text-3xl font-black tracking-tight mb-1">{{ $t('dashboard.title') }}<span
                        class="text-blue-500">{{
                            userStore.userName }}</span></h2>
                <p class="text-[#9dabb9]">{{ $t('dashboard.subtitle') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-background-light dark:bg-[#101922] border border-[#3b4754] p-5 rounded-xl shadow-smbg-[#101922]">
                    <p class="text-[#9dabb9] text-xs font-bold uppercase tracking-wider mb-1">
                        {{ $t('dashboard.totalTeams') }}
                    </p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold">{{ numbersData.total_teams }}</span>
                        <span class="text-emerald-400 text-sm font-medium">{{ $t('dashboard.teams') }}</span>
                    </div>
                </div>
                <div
                    class="bg-background-light dark:bg-[#101922] border border-[#3b4754] p-5 rounded-xl shadow-smbg-[#101922]">
                    <p class="text-[#9dabb9] text-xs font-bold uppercase tracking-wider mb-1"> {{
                        $t('dashboard.activeTasks') }}
                    </p>
                    <div class="flex items-baseline gap-2" v-if="numbersData.active_tasks !== 0">
                        <span class="text-3xl font-bold">{{ numbersData.active_tasks }}</span>
                        <span class="text-emerald-400 text-sm font-medium">{{ $t('dashboard.tasks') }}</span>
                    </div>
                    <div class="flex items-baseline gap-2" v-else>
                        <span class="text-3xl font-bold">-</span>
                        <span class="text-emerald-400 text-sm font-medium">{{ $t('dashboard.createFirstTask') }}</span>
                    </div>
                </div>
                <div
                    class="bg-background-light dark:bg-[#101922] border border-[#3b4754] p-5 rounded-xl shadow-smbg-[#101922]">
                    <p class="text-[#9dabb9] text-xs font-bold uppercase tracking-wider mb-1">{{
                        $t('dashboard.pendingInvites') }}
                    </p>
                    <div class="flex items-baseline gap-2" v-if="numbersData.joinRequests > 0">
                        <span class="text-3xl font-bold">{{ numbersData.joinRequests }}</span>
                        <span class="text-[#9dabb9] text-sm font-medium">{{ $t('dashboard.needsAction') }}</span>
                    </div>
                    <div class="flex items-baseline gap-2" v-else>
                        <span class="text-3xl font-bold">-</span>
                        <span class="text-[#9dabb9] text-sm font-medium">{{ $t('dashboard.needsAction') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
            <!-- Team Cards Grid -->
            <my-teams-sumarize />
            <!-- Side Panels: Join Requests -->
            <latest-join-requests />
        </div>
    </div>
    <!-- </div> -->
</template>