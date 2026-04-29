<script setup lang="ts">
import NavBar from '@/components/NavBar.vue';
import api from '@/utils/api';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute()
const router = useRouter()
const teamID = route.params.id
console.log(route.params.id);


const team = ref({
    name: '',
    description: null
});

const fetchTeam = async () => {
    const res = await api.get(`/teams/${teamID}`)
    team.value.name = res.data.data.name
    team.value.description = res.data.data.description
}


const handleUpdateTeam = () => {
    if (team.value.name === '') return;
    try {
        api.put(`/teams/${teamID}`, { name: team.value.name, description: team.value.description })
        router.push({ name: 'my-teams' })
    }
    catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    fetchTeam()
})
</script>

<template>
    <NavBar :title="$t('nav.updateTeamTitle')" />
    <div class="p-8 flex flex-col xl:flex-row gap-8">
        <!-- Left Column: Create Team Section (Primary Focus) -->
        <!-- 
            Primary /5 = > #1E2D45
            Primary /30 = > #1F4371 
         -->
        <div class="flex-1 max-w-3xl">
            <div class="bg-[#1e293b] rounded-xl border-2 border-[#1F4371] shadow-xl shadow-[#1E2D45] overflow-hidden">
                <div class="p-4 border-b  border-[#283039] flex items-center justify-between bg-[#1E2D45]">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500 text-2xl">group_add</span>
                        <h2 class="text-sm sm:text-xl font-bold">{{ $t('updateTeamPage.title') }}</h2>
                    </div>
                    <span
                        class="hidden sm:block text-[10px] font-bold uppercase tracking-widest px-2 py-1 bg-[#137fec] text-white rounded">{{
                            $t('updateTeamPage.activeStep') }}</span>
                </div>
                <div class="p-4 space-y-8 bg-[#1E293B]">
                    <!-- Team Identity Row -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">

                        <div class="flex-1 space-y-6 w-full">
                            <div class="space-y-2">
                                <label class="text-xs sm:text-sm font-semibold  text-slate-300">{{
                                    $t('updateTeamPage.teamName') }}</label>
                                <input
                                    class="w-full  bg-[#283039]  border-transparent rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#137fec] border-none text-xs sm:text-base outline-none transition-all"
                                    placeholder="e.g. Design Systems" type="text" v-model="team.name" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs sm:text-sm font-semibold  text-slate-300">{{
                                    $t('updateTeamPage.teamDec') }}</label>
                                <textarea
                                    class="w-full  bg-[#283039]  border-transparent rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#137fec] border-none text-xs sm:text-base outline-none transition-all resize-none"
                                    placeholder="What will this team achieve together?"
                                    rows="3">{{ team.description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <hr class=" border-[#283039]" />
                    <!-- Invite Members Section -->

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button
                            class="px-8 py-3 bg-[#137fec] hover:bg-[#137fec]/90 rounded-xl font-bold text-xs sm:text-sm text-white shadow-lg shadow-[#137fec]/25 transition-all flex items-center gap-2 cursor-pointer"
                            @click="handleUpdateTeam">
                            {{ $t('updateTeamPage.updateTeamSpace') }}
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>