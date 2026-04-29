<script setup lang="ts">

import { useRouter } from 'vue-router';
import { useUserStore } from '@/stores/user';
import api from '@/utils/api';
const userStore = useUserStore();
const router = useRouter();

const handleLogout = () => {
    api.post('/logout').catch((error) => {
        console.error('Logout failed:', error.response?.data?.message || error.message);
    });
    userStore.logout();
    // window.location.href = '/registration';
}
</script>
<template>
    <main class="flex-1 flex flex-col min-w-0 bg-background-dark h-full relative">
        <header
            class="flex items-center justify-between px-8 py-4 bg-[#101922] backdrop-blur-md sticky top-0 z-10 border-b border-[#1E2D3D]">
            <div class="flex items-center gap-4">

                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <h2 class="text-white text-lg font-bold leading-tight">Vue.js Core Development</h2>
                        <span
                            class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[10px] font-mono font-bold border border-[#1E2D3D]">#TEAM-1234</span>
                    </div>
                    <p class="text-xs text-slate-400">Manage incoming access requests for this team</p>
                </div>
            </div>
            <div class="flex items-center gap-3 ml-4">

                <button class="p-2 text-[#9dabb9] hover:text-white rounded-lg hover:bg-[#283039] cursor-pointer">
                    <span class="material-symbols-outlined text-red-500" v-if="userStore.notifications.length > 1">
                        notifications_unread
                    </span>
                    <span class="material-symbols-outlined" v-else>
                        notifications
                    </span>
                </button>
                <div class="h-8 w-[1px] bg-[#283039] mx-2"></div>
                <button
                    class="p-2 text-[#9dabb9] hover:text-red-800 rounded-lg hover:bg-[#283039] cursor-pointer duration-200"
                    @click="handleLogout">
                    <span class="material-symbols-outlined">
                        logout
                    </span>
                </button>
            </div>
        </header>
        <div class="flex-1 overflow-y-auto overflow-x-hidden p-6 md:p-8">
            <div class="max-w-5xl mx-auto flex flex-col gap-6">
                <div class="flex flex-col gap-1">
                    <h3 class="text-white text-2xl font-black tracking-tight flex items-center gap-3">
                        Join Requests
                        <span
                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-[#152A4A] text-blue-500 text-sm font-bold">2
                            Pending</span>
                    </h3>
                    <p class="text-slate-400 text-sm">Reviewing candidates for the <span
                            class="text-slate-200 font-medium">Vue.js Core Development</span> team.</p>
                </div>
                <div
                    class="flex flex-col lg:flex-row gap-4 lg:items-center justify-between bg-[#162330] p-2 rounded-xl border border-[#1E2D3D]">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-slate-500 text-[20px]">search</span>
                        </div>
                        <input
                            class="block w-full pl-10 pr-3 py-2 border-none rounded-lg leading-5 bg-[#0D141C] text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary/50 sm:text-sm transition-shadow"
                            placeholder="Filter team applicants..." type="text" />
                    </div>
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0 no-scrollbar px-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest mr-2">Quick
                            Filter:</span>
                        <button
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-500 text-white text-xs font-bold transition-colors whitespace-nowrap shadow-sm shadow-primary/20">
                            Recent
                        </button>
                        <button
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#0D141C] text-slate-400 text-xs font-medium hover:bg-slate-800 transition-colors whitespace-nowrap border border-[#1E2D3D]">
                            Referred
                        </button>
                        <button
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#0D141C] text-slate-400 text-xs font-medium hover:bg-slate-800 transition-colors whitespace-nowrap border border-[#1E2D3D]">
                            High Experience
                        </button>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <div
                        class="group flex flex-col md:flex-row items-start md:items-center justify-between p-5 bg-[#162330] rounded-xl border border-[#1E2D3D] shadow-sm hover:border-primary/40 transition-all duration-200">
                        <div class="flex items-center gap-4 w-full md:w-auto">
                            <div class="relative shrink-0">
                                <div class="size-14 rounded-xl bg-cover bg-center border border-[#1E2D3D] shadow-sm"
                                    data-alt="Profile picture"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCJm0sI7KW8WtLBpfrxJHCxHlXzcFBYnE2s7FNGioeANOnLwDrbmsn8Zp9qrmbZaFE-7Ml5-79BVcf1jfBObc_Wjz_3EYUZv1j0py0h3_kyO2IoH8W7Mg9B7If008UtNvPFQf0qpT_vQTwbeKZYn3HZ9Mnzi7SuD21WDqVKMzQM-t5-g0dBb-c4mlIfsTFja1z8Ebp6agrlLfFOWkUje4yawVrE-zppAwcaGDlEZ062VDlebr9CmoiwaR7a4IdlOPe0q39_M5sEkkU_");'>
                                </div>
                                <div class="absolute -top-1 -right-1">

                                </div>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-white font-bold text-base">Alex Johnson</h3>
                                    <span
                                        class="px-2 py-0.5 rounded bg-[#182A43] text-blue-500 text-[10px] uppercase font-bold tracking-wider border border-[#1B3965]">Senior
                                        Frontend</span>
                                </div>
                                <p class="text-slate-400 text-sm">alex.j@example.com</p>
                                <div class="flex items-center gap-3 text-slate-500 text-xs mt-1">
                                    <span class="flex items-center gap-1"><span
                                            class="material-symbols-outlined text-[14px]">calendar_today</span> Oct
                                        24</span>
                                    <span class="flex items-center gap-1 text-blue-500"><span
                                            class="material-symbols-outlined text-[14px]">link</span> Referred by
                                        Sarah C.</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-4 md:mt-0 w-full md:w-auto">
                            <button
                                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-red-500/30 text-red-400 hover:bg-red-500/10 text-sm font-semibold transition-colors">
                                <span class="material-symbols-outlined text-[18px]">block</span>
                                Reject
                            </button>
                            <button
                                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-lg bg-blue-500 hover:bg-blue-800 text-white shadow-lg shadow-primary/20 text-sm font-bold transition-all transform active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">person_check</span>
                                Approve
                            </button>
                        </div>
                    </div>
                    <div
                        class="group flex flex-col md:flex-row items-start md:items-center justify-between p-5 bg-[#162330] rounded-xl border border-[#1E2D3D] shadow-sm hover:border-primary/40 transition-all duration-200">
                        <div class="flex items-center gap-4 w-full md:w-auto">
                            <div class="relative shrink-0">
                                <div class="size-14 rounded-xl bg-cover bg-center border border-[#1E2D3D] shadow-sm"
                                    data-alt="Profile picture"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAizMfJjHir-SQaGGfs_kAKg8drwjRs1EjHAj2e05RsFEkLjSE6G7YsWZIuDO36ZCAaGc9w1au21hu-japXDAWlnTU8-hXqUu5tk19Yb6UllB_XbSkLUk7GTdVK-sbYcwVNbnqmTuMFDv3uqAnzoYle9Lg7aDltSjQPMcuWQ7DaoUn_yn1v86QutfygsqdC_EMxAUYNKDQ2Ur8dl3ARJvsviFbTCM3_qdPRPR1cosfxtwKe6MAoY_YtwMgls0MK2pgrDthnhG0I1Ltj");'>
                                </div>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-white font-bold text-base">Michael Brown</h3>
                                    <span
                                        class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[10px] uppercase font-bold tracking-wider border border-[#1E2D3D]">Core
                                        Contributor</span>
                                </div>
                                <p class="text-slate-400 text-sm">michael.b@example.com</p>
                                <div class="flex items-center gap-3 text-slate-500 text-xs mt-1">
                                    <span class="flex items-center gap-1"><span
                                            class="material-symbols-outlined text-[14px]">calendar_today</span> Oct
                                        22</span>
                                    <span class="flex items-center gap-1"><span
                                            class="material-symbols-outlined text-[14px]">public</span> Direct
                                        Application</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-4 md:mt-0 w-full md:w-auto">
                            <button
                                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-red-500/30 text-red-400 hover:bg-red-500/10 text-sm font-semibold transition-colors">
                                <span class="material-symbols-outlined text-[18px]">block</span>
                                Reject
                            </button>
                            <button
                                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-lg bg-blue-500 hover:bg-blue-800 text-white shadow-lg shadow-primary/20 text-sm font-bold transition-all transform active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">person_check</span>
                                Approve
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</template>