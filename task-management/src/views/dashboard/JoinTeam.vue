<script setup>
import { ref } from 'vue'
// import axios from '@/api/axios' // لو عندك instance

import api from '@/utils/api'
import NavBar from '@/components/NavBar.vue'

const joinCode = ref('')
const state = ref('idle') // idle | loading | success | error
const team = ref({})
const show = ref('search')

const checkCode = async () => {
    state.value = 'loading'

    try {
        const res = await api.post('/teams/join', {
            join_code: joinCode.value
        })

        team.value = res.data.team
        state.value = 'success'


    } catch (error) {
        state.value = 'error'
        show.value = 'error'
    }
    finally {
        team.value ? team.value === '' ? show.value = 'isMember' : show.value = 'joining' : show.value = 'error'


    }
}




const requestJoin = async () => {
    await api.post(`/teams/join-request`, {
        team_id: team.value.id
    })
    show.value = 'done'
    reset()
}

const reset = () => {
    joinCode.value = ''
    team.value = {}
    state.value = 'idle'
}
</script>

<template>
    <NavBar title="Jion Team" description="Send Code and join teams" />
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-hidden bg-[#111F23]">
        <!-- Abstract Background Gradient Decoration -->
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] opacity-5 pointer-events-none">
            <div class="w-full h-full rounded-full bg-[#11B7EC] blur-[120px]"></div>
        </div>


        <!-- Check Join Code Card -->
        <div class="relative w-full max-w-[520px] bg-[#15262C] border border-[#283539] rounded-xl shadow-2xl p-8 sm:p-10 lg:p-12 flex flex-col gap-8 z-10"
            v-if="show === 'search'">
            <!-- Header -->
            <div class="space-y-3 text-center">
                <div
                    class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#11B7EC]/10 text-[#11B7EC] mb-2 ring-1 ring-[#11B7EC]/20">
                    <span class="material-symbols-outlined text-[28px]">group_add</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Join a Team</h1>
                <p class="text-slate-400 text-sm sm:text-base max-w-sm mx-auto leading-relaxed">
                    Collaborate with your colleagues in real-time. Enter the unique code shared by your team admin to
                    get started.
                </p>
            </div>
            <!-- Form -->
            <div class="space-y-6">
                <div class="space-y-2 group">
                    <label
                        class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1 transition-colors group-focus-within:text-[#11B7EC]"
                        for="team-code">
                        Team Invitation Code
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span
                                class="material-symbols-outlined text-slate-500 group-focus-within:text-[#11B7EC] transition-colors">key</span>
                        </div>
                        <input
                            class="block w-full h-14 pl-12 pr-4 bg-[#111618] border border-[#283539] text-white rounded-lg placeholder-slate-600 focus:outline-none focus:border-[#11B7EC] focus:ring-1 focus:ring-[#11B7EC]/50 transition-all duration-200 text-lg font-mono tracking-wide shadow-sm"
                            id="team-code" placeholder="e.g. TM-8492-X" type="text" v-model="joinCode" />
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none opacity-0 group-focus-within:opacity-100 transition-opacity">
                            <div class="h-2 w-2 rounded-full bg-[#11B7EC] animate-pulse"></div>
                        </div>
                    </div>
                </div>
                <button
                    class="w-full h-12 bg-[#11B7EC] hover:bg-[#13b6ec]/90 active:scale-[0.98] text-[#111618] font-bold rounded-lg transition-all duration-200 shadow-lg shadow-[#11B7EC]/20 flex items-center justify-center gap-2 group/btn cursor-pointer"
                    @click="checkCode">
                    <span>Check Code</span>
                    <span
                        class="material-symbols-outlined text-lg transition-transform duration-200 group-hover/btn:translate-x-1">arrow_forward</span>
                </button>
            </div>
            <!-- Footer / Helper -->
            <div class="pt-4 border-t border-[#283539] flex flex-col items-center gap-4 text-center">
                <router-link :to="{ name: 'my-teams' }"
                    class="text-xs font-medium text-slate-500 hover:text-slate-300 transition-colors">
                    Back To My Teams
                </router-link>
            </div>
        </div>

        <!-- Wrong Join Code Card  -->
        <div class="relative w-full max-w-[520px] bg-[#16262c] border border-[#283539] rounded-xl shadow-2xl p-8 sm:p-10 lg:p-12 flex flex-col gap-8 z-10"
            v-if="show === 'error'">
            <div class="space-y-3 text-center">
                <div
                    class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#ef4444]/10 text-[#ef4444] mb-2 ring-1 ring-[#ef4444]/20">
                    <span class="material-symbols-outlined text-[28px]">warning</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Team Not Found</h1>
                <p class="text-slate-400 text-sm sm:text-base max-w-sm mx-auto leading-relaxed">
                    We couldn't find any team with the code you entered. Please double-check and try again.
                </p>
            </div>
            <div class="space-y-6">
                <div class="space-y-2 group">
                    <label
                        class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1 transition-colors group-focus-within:text-[#ef4444]"
                        for="team-code">
                        Team Invitation Code
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span
                                class="material-symbols-outlined text-slate-500 group-focus-within:text-[#ef4444] transition-colors">key</span>
                        </div>
                        <input
                            class="block w-full h-14 pl-12 pr-4 bg-[#111618] border border-[#ef4444]/50 text-white rounded-lg placeholder-slate-600 focus:outline-none focus:border-[#ef4444] focus:ring-1 focus:ring-[#ef4444]/50 transition-all duration-200 text-lg font-mono tracking-wide shadow-sm"
                            id="team-code" placeholder="e.g. TM-8492-X" type="text" v-model="joinCode" />
                        <div
                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-[#ef4444]">
                            <span class="material-symbols-outlined text-xl">error</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <button
                        class="w-full h-12 bg-[#13b6ec] hover:bg-[#13b6ec]/90 active:scale-[0.98] text-[#111618] font-bold rounded-lg transition-all duration-200 shadow-lg shadow-[#13b6ec]/20 flex items-center justify-center gap-2 group/btn cursor-pointer"
                        @click="checkCode">
                        <span>Try Again</span>
                        <span
                            class="material-symbols-outlined text-lg transition-transform duration-200 group-hover/btn:rotate-180">refresh</span>
                    </button>
                </div>
            </div>
            <div class="pt-4 border-t border-[#283539] flex flex-col items-center gap-4 text-center">
                <router-link :to="{ name: 'my-teams' }"
                    class="text-xs font-medium text-slate-500 hover:text-slate-300 transition-colors">
                    Back To My Teams
                </router-link>
            </div>
        </div>

        <!-- Member Card -->
        <div class="relative w-full max-w-[520px] bg-[#16262c] border border-[#283539] rounded-xl shadow-2xl p-8 sm:p-10 lg:p-12 flex flex-col gap-8 z-10"
            v-if="show === 'isMember'">
            <div class="space-y-4 text-center">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#13b6ec]/10 text-[#13b6ec] mb-2 ring-1 ring-[#13b6ec]/20 relative">
                    <span class="material-symbols-outlined text-[32px]">info</span>
                </div>
                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Already a Member</h1>
                    <p class="text-slate-400 text-sm sm:text-base max-w-sm mx-auto leading-relaxed">
                        You are already a member of this team. You can access it directly from your dashboard.
                    </p>
                </div>
            </div>
            <div class="space-y-6">
                <div class="p-4 rounded-lg bg-[#111618] border border-[#283539] flex items-center gap-4">
                    <div
                        class="size-12 rounded-lg bg-gradient-to-br from-[#13b6ec] to-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                        T
                    </div>
                    <div class="flex-1 text-left">
                        <p class="text-white font-semibold text-base">Product Development Team</p>
                        <p class="text-slate-500 text-xs uppercase tracking-wider font-bold">Member Since Oct 2023</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <button
                        class="w-full h-12 bg-[#13b6ec] hover:bg-[#13b6ec]/90 active:scale-[0.98] text-[#111618] font-bold rounded-lg transition-all duration-200 shadow-lg shadow-[#13b6ec]/20 flex items-center justify-center gap-2 group/btn cursor-pointer">
                        <span>Go to Team Workspace</span>
                        <span
                            class="material-symbols-outlined text-lg transition-transform duration-200 group-hover/btn:translate-x-1">arrow_forward</span>
                    </button>
                </div>
            </div>
            <div class="pt-4 border-t border-[#283539] flex flex-col items-center gap-4 text-center">
                <router-link :to="{ name: 'dashboard' }"
                    class="text-xs font-medium text-slate-500 hover:text-slate-300 transition-colors">
                    Back to Dashboard
                </router-link>
            </div>
        </div>

        <!-- Joining Card -->
        <div class="w-full max-w-lg bg-surface-dark rounded-xl shadow-2xl border border-[#28392e] overflow-hidden relative z-10 card-inner-shadow transform transition-all duration-500 ease-out"
            v-if="show === 'joining' && team">
            <!-- Glassmorphism Header -->
            <div class="glass-header px-8 py-5 flex items-center justify-between absolute w-full top-0 left-0 z-20">
                <span class="uppercase tracking-widest text-[11px] font-bold text-[#9db9a6]">Join Request</span>
                <div
                    class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-900/30 border border-green-800/50">
                    <span class="material-symbols-outlined text-[#13ec5b] text-[16px]">check_circle</span>
                    <span class="text-xs font-semibold text-[#13ec5b]">Team Found</span>
                </div>
            </div>
            <!-- Card Body -->
            <div class="pt-24 pb-8 px-8 flex flex-col">
                <!-- Team Identity Section -->
                <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center mb-8">
                    <!-- Avatar -->
                    <div class="relative group shrink-0">
                        <div class="size-24 rounded-lg bg-surface-dark-highlight bg-cover bg-center avatar-glow border-2border-[#28392e]"
                            data-alt="Engineering team logo featuring abstract geometric shapes"
                            style='background-image: url("/src/assets/partners.png");'>
                        </div>
                        <!-- Team Icon -->
                        <!-- <div
                            class="absolute -bottom-2 -right-2 bg-[#13ec5b] text-black rounded-full p-1 border-2border-surface-dark">
                            <span
                                class="material-symbols-outlined text-[16px] leading-none block font-bold">group</span>
                        </div> -->
                    </div>
                    <!-- Typography Stack -->
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <h2 class="text-2xl font-bold text-white tracking-tight">{{ team.name }}</h2>
                            <span class="material-symbols-outlined text-[#13ec5b] text-[20px]"
                                title="Verified Team">verified</span>
                        </div>
                        <p class="text-sm text-[#9db9a6] font-medium">{{ team.members }}
                            Active Members</p>
                        <p class="text-xs text-gray-500 mt-1">Created Oct 2023</p>
                    </div>
                </div>
                <!-- Description / Context -->
                <div class="bg-black/20 rounded-lg p-4 mb-8 border border-white/5">
                    <p class="text-sm text-gray-300 leading-relaxed">
                        You are about to join the <span class="text-white font-semibold">{{ team.name }}</span>
                        workspace.
                        This will give you access to all shared project boards.
                    </p>
                </div>
                <!-- Actions -->
                <div class="flex flex-col gap-4">
                    <button
                        class="group relative w-full h-12 flex items-center justify-center gap-2 bg-[#13ec5b] hover:bg-[#0fdc52] active:scale-[0.99] transition-all rounded-lg text-[#111813] font-bold text-base tracking-wide shadow-[0_0_20px_rgba(19,236,91,0.3)] hover:shadow-[0_0_25px_rgba(19,236,91,0.5)] cursor-pointer"
                        @click="requestJoin()">
                        <span>Request to Join</span>
                        <span
                            class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </button>
                    <button
                        class="text-sm font-medium text-[#6b7d72] hover:text-white transition-colors flex items-center justify-center gap-2 py-2 cursor-pointer"
                        @click="show = 'search'">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        Change Code
                    </button>
                </div>
            </div>
            <!-- Bottom decorative subtle border -->
            <div
                class="absolute bottom-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-[#13ec5b]/30 to-transparent">
            </div>
        </div>

        <!-- Successed Card -->
        <div class="relative w-full max-w-[520px] bg-[#16262c] border border-[#283539] rounded-xl shadow-2xl p-8 sm:p-10 lg:p-12 flex flex-col gap-8 z-10"
            v-if="show === 'done'">
            <div class="space-y-4 text-center">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-[#22c55e]/10 text-[#22c55e] mb-2 ring-1 ring-[#22c55e]/30 shadow-[0_0_30px_-5px_rgba(34,197,94,0.3)]">
                    <span class="material-symbols-outlined text-[48px]">check</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight pt-2">Request Sent!</h1>
                <p class="text-slate-400 text-sm sm:text-base max-w-sm mx-auto leading-relaxed">
                    Your request to join the team has been sent to the administrators. You will be notified once they
                    approve it.
                </p>
            </div>
            <div class="space-y-6">
                <router-link :to="{ name: 'my-teams' }"
                    class="w-full h-12 bg-[#13b6ec] hover:bg-[#13b6ec]/90 active:scale-[0.98] text-[#111618] font-bold rounded-lg transition-all duration-200 shadow-lg shadow-[#13b6ec]/20 flex items-center justify-center gap-2 group/btn cursor-pointer">
                    <span>
                        Back To My Teams
                    </span>
                </router-link>
            </div>

        </div>
    </main>
</template>
