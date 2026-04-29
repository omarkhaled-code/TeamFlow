<script setup lang="ts">
import NavBar from '@/components/NavBar.vue';
import TeamCard from '@/components/TeamCard.vue';
import api from '@/utils/api';
import { RouterLink } from 'vue-router';

import { ref, onMounted, onUnmounted } from 'vue';
import { useTeamStore } from '@/stores/team';
import CreateTask from '@/components/Teleports/CreateTask.vue';
import SendJoinRequest from '@/components/SendJoinRequest.vue';
import { useRouter } from 'vue-router';
import Echo from '@/echo';
import RequireUpgrade from '@/components/Teleports/RequireUpgrade.vue';

const router = useRouter()

const teamStore = useTeamStore()
const teams = ref([])
const colorTehmes = ['indigo', 'green', 'yellow', 'red', 'purple'];
const showRequireUpgrade = ref(false);


const getUserTeams = async () => {
    await teamStore.fetchUserTeams()
    teams.value = teamStore.getUserTeams

};

const teamColorTeheme = (index = 1) => {
    return colorTehmes[index % colorTehmes.length];
}

let channel;
onMounted(() => {
    getUserTeams();

    channel = Echo.channel('team-channel')
        .listen('.team.events', (e) => {
            getUserTeams()
        })
});

onUnmounted(() => {
    if (channel) {
        channel.stopListening('.team.events')
    }
})


// Check Plan fucntion has a defulat value called team, and she takes ['create', 'join']
const checkPlan = async (typeOfCheck = 'create') => {
    const { data } = await api.post(`/user/check-${typeOfCheck}-team`);
    if (data.allowed === false) {
        showRequireUpgrade.value = true
    } else {
        router.push({ name: `${typeOfCheck}-team` })
    }
}
</script>

<template>
    <NavBar :title="$t('nav.myTeamsTitle')" :description="$t('nav.myTeamsDec')" />
    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-start gap-2 sm:gap-0 sm:items-center justify-between mb-6 flex-col sm:flex-row">
                <div>
                    <p class="text-xs sm:text-sm text-[#9dabb9]">{{ $t('myTeamsPage.showing') }} <span
                            class="text-white sm:font-bold">{{
                                teams.length
                            }}</span>

                        {{ $t('myTeamsPage.activeTeams') }}</p>
                </div>
                <button
                    class="bg-blue-600 py-2 px-4 text-xs sm:text-sm rounded-lg cursor-pointer hover:bg-blue-700 active:bg-blue-600 duration-200"
                    @click="checkPlan('join')">
                    {{ $t('myTeamsPage.joinNewTeam') }}
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4">
                <button class="bg-transparent border-2 border-dashed border-[#283039] hover:border-blue-500/50
                        transition-all rounded-xl p-5 flex flex-col items-center justify-center text-center
                        cursor-pointer group" @click="checkPlan('create')">
                    <div
                        class="h-12 w-12 rounded-full bg-[#283039] group-hover:bg-blue-700/20 flex items-center justify-center mb-3 transition-colors">
                        <span class="material-symbols-outlined text-[#9dabb9] group-hover:text-blue-500">add</span>
                    </div>
                    <h4 class="font-bold text-[#9dabb9] group-hover:text-white transition-colors">
                        {{ $t('myTeamsPage.CreateNewTeamTittle') }}</h4>
                    <p class="text-xs text-[#9dabb9] mt-1">{{ $t('myTeamsPage.CreateNewTeamDec') }}</p>
                </button>
                <!-- First Team -->
                <team-card v-for="team in teams" :key="team.id" :team-id="team.id" :team-title="team.name"
                    :team-description="team.description" :color-theme="teamColorTeheme(teams.indexOf(team))"
                    :done-tasks="team.doneTasksCount" :total-tasks="team.tasks_count"
                    :members-count="team.users_count" />
            </div>
        </div>
    </div>
    <SendJoinRequest />

    <RequireUpgrade v-if="showRequireUpgrade" v-model:show="showRequireUpgrade" />


    <!-- <UpdateTask v-model:show="showUpdateTask" :teamId @re-fetch-tasks="fetchTasks" v-if="updateTaskId" -->
</template>