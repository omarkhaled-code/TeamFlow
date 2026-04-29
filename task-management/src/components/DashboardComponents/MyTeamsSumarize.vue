<script setup>
import { RouterLink } from 'vue-router';
import TeamCard from '../TeamCard.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import api from '@/utils/api';
import Echo from '@/echo';
import EmptyTeamCard from '../EmptyTeamCard.vue';

const colorTehmes = ['indigo', 'green', 'yellow', 'red', 'purple'];
const teams = ref([])
const getUserSumarizeTeams = async () => {
    const { data } = await api.get('/user/teams/summarize');
    teams.value = data.data;
}
const teamColorTeheme = (index = 1) => {
    return colorTehmes[index % colorTehmes.length];
}

let channel;

onMounted(() => {
    getUserSumarizeTeams();

    channel = Echo.channel('team-channel')
        .listen('.team.events', (e) => {
            getUserSumarizeTeams()

        })
});

onUnmounted(() => {
    if (channel) {
        channel.stopListening('.team.events')
    }
})

</script>
<template>
    <div class="xl:col-span-2 space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold">{{ $t('dashboard.teamSumarizeTitle') }}</h3>
            <router-link :to="{ name: 'my-teams' }"
                class="text-blue-500 text-sm font-bold hover:underline cursor-pointer" v-if="teams.length > 1">{{
                    $t(Dashboard.viewAllTeams) }}</router-link>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Team Card 1 -->
            <team-card v-for="team in teams" :key="team.id" :team-id="team.id" :team-title="team.name"
                :team-description="team.description" :color-theme="teamColorTeheme(teams.indexOf(team))"
                :done-tasks="team.doneTasksCount" :total-tasks="team.tasks_count" :members-count="team.users_count"
                v-if="teams.length > 0" />

            <!-- Emtpy Team Card -->
            <empty-team-card v-else />

            <!-- Add Team Button (Grid Item) -->
            <router-link :to="{ name: 'create-team' }" class="bg-transparent border-2 border-dashed border-[#283039] hover:border-blue-500/50
                        transition-all rounded-xl p-5 flex flex-col items-center justify-center text-center
                        cursor-pointer group" v-if="teams.length < 1">
                <div
                    class="h-12 w-12 rounded-full bg-[#283039] group-hover:bg-blue-700/20 flex items-center justify-center mb-3 transition-colors">
                    <span class="material-symbols-outlined text-[#9dabb9] group-hover:text-blue-500">add</span>
                </div>
                <h4 class="font-bold text-[#9dabb9] group-hover:text-white transition-colors">Create
                    New
                    Team</h4>
                <p class="text-xs text-[#9dabb9] mt-1">Start a fresh workspace</p>
            </router-link>
        </div>
    </div>
</template>