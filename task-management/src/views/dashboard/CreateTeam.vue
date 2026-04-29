<script setup lang="ts">
import NavBar from '@/components/NavBar.vue';
import { ref } from 'vue';

import { useRouter } from 'vue-router';
import { useTeamStore } from '@/stores/team';

const teamStore = useTeamStore()
const router = useRouter();
const team = ref({
    name: '',
    description: ''
});

const error = ref("")

const handleCreateTeam = () => {
    // Logic to handle team creation
    if (team.value.name === '') return error.value = 'the team name should not be empty!';

    try {
        teamStore.createTeam({ name: team.value.name, description: team.value.description })
    } catch (error) {
        console.error('Unexpected error:', error);
    }
    finally {
        team.value.name = '';
        team.value.description = '';

        router.push({ name: 'my-teams' });
    }

};
</script>

<template>
    <NavBar :title="$t('nav.createTeamTitle')" :description="$t('nav.createTeamDec')" />
    <div class="p-4 flex flex-col xl:flex-row gap-8">
        <!-- Left Column: Create Team Section (Primary Focus) -->
        <!-- 
            Primary /5 = > #1E2D45
            Primary /30 = > #1F4371 
         -->
        <div class="flex-1 max-w-3xl">
            <div class="bg-[#1e293b] rounded-xl border-2 border-[#1F4371] shadow-xl shadow-[#1E2D45] overflow-hidden">
                <div class="p-6 border-b  border-[#283039] flex items-center justify-between bg-[#1E2D45]">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500 text-2xl">group_add</span>
                        <h2 class="text-sm sm:text-xl font-bold">{{ $t('createTeamPage.title') }}</h2>
                    </div>
                    <span
                        class="hidden sm:block text-[10px] font-bold uppercase tracking-widest px-2 py-1 bg-[#137fec] text-white rounded">{{
                            $t('createTeamPage.activeStep') }}</span>
                </div>
                <div class="p-8 space-y-8 bg-[#1E293B]">
                    <!-- Team Identity Row -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">

                        <div class="flex-1 space-y-6 w-full">
                            <div class="space-y-2">
                                <label class="text-xs sm:text-sm font-semibold  text-slate-300">{{
                                    $t('createTeamPage.teamName') }}</label>
                                <input
                                    class="w-full bg-[#283039]  rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#137fec] text-xs sm:text-base outline-none transition-al"
                                    :class="error && 'border border-red-500 focus:border-none'"
                                    placeholder="e.g. Design Systems" type="text" v-model="team.name"
                                    @input="error = ''" />
                                <p class="text-red-500 text-sm">{{ error }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs sm:text-sm font-semibold  text-slate-300">{{
                                    $t('createTeamPage.teamDec') }}</label>
                                <textarea
                                    class="w-full  bg-[#283039]  border-transparent rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#137fec] border-none text-xs sm:text-base outline-none transition-all resize-none"
                                    placeholder="What will this team achieve together?" rows="3"
                                    v-model="team.description"></textarea>
                            </div>
                        </div>
                    </div>
                    <hr class=" border-[#283039]" />
                    <!-- Invite Members Section -->

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button
                            class="px-3 sm:px-8 py-3 bg-[#137fec] hover:bg-[#137fec]/70 rounded-xl font-bold text-xs sm:text-sm text-white shadow-lg shadow-[#137fec]/5 transition-all flex items-center gap-2 cursor-pointer"
                            @click="handleCreateTeam">

                            {{ $t('createTeamPage.createTeamSpace') }}

                            <span class="material-symbols-outlined">arrow_forward</span>


                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>