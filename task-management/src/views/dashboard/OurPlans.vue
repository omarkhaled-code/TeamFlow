<script setup lang="ts">
import NavBar from '@/components/NavBar.vue';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

import { useUserStore } from '@/stores/user';
const router = useRouter()
const userStore = useUserStore()

const activePlan = ref(userStore.userPlan)

const getUserPlan = async () => {
    // get user plan from backend, and store value in activePlan!
}

const handleUpgradePlan = (newPlan) => {

    if (activePlan.value === newPlan) return;
    if (activePlan.value === 'pro') {
        // send post request to backend to check if user is has buy pro plan before or this is first time!
    }

    router.push({ name: 'checkout' })
}
</script>


<template>
    <NavBar :title="$t('nav.ourPlansTitle')" :description="$t('nav.ourPlansDec')" />
    <main class="flex-1 overflow-y-auto bg-[#111418] scroll-smooth">
        <div class="py-8 md:py-12 px-4 md:px-12 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 gap-10 items-stretch pricing-grid mx-auto"
                :class="activePlan === 'pro' ? 'md:grid-cols-1' : 'lg:grid-cols-2'">
                <div class="glass-card rounded-[2rem] p-10 flex flex-col transition-all duration-300 hover:border-white/20"
                    :class="activePlan === 'free' && 'pro-glow'" v-if="activePlan === 'free'">
                    <div class="absolute -top-4 right-10 bg-[#10b981] text-black px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest shadow-[0_0_20px_rgba(16,185,129,0.4)]"
                        v-if="activePlan === 'free'">
                        {{ $t('ourPlans.currentPlan') }}
                    </div>
                    <div class="mb-10">
                        <div class="inline-block px-3 py-1 rounded-md text-xs font-bold uppercase tracking-widest mb-4"
                            :class="activePlan === 'free' ? 'bg-[#10b981]/10 text-[#10b981]' : ' bg-white/5 text-slate-400'">
                            {{ $t('ourPlans.freePlan.title') }}</div>
                        <h3 class="text-white font-bold text-2xl mb-2">{{ $t('ourPlans.freePlan.free') }}</h3>
                        <div class="flex items-baseline gap-1 mb-4">
                            <span class="text-5xl font-black text-white">$0</span>
                            <span class="text-slate-400 font-medium">/{{ $t('ourPlans.mo') }}</span>
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed">{{ $t('ourPlans.freePlan.dec') }}</p>
                    </div>
                    <div class="space-y-6 mb-12 flex-1">
                        <div class="flex items-center gap-4">
                            <!-- <span class="material-symbols-outlined text-[#10b981]">check_circle</span> -->
                            <span class="material-symbols-outlined"
                                :class="activePlan === 'free' ? 'text-[#10b981]' : 'text-slate-500'">check_circle</span>

                            <span class="text-sm text-slate-300">{{ $t('ourPlans.rules.maxCreated') }}: <span
                                    class="text-white font-bold">1</span></span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined"
                                :class="activePlan === 'free' ? 'text-[#10b981]' : 'text-slate-500'">check_circle</span>
                            <span class="text-sm text-slate-300">{{ $t('ourPlans.rules.maxJoined') }}: <span
                                    class="text-white font-bold">3</span></span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined"
                                :class="activePlan === 'free' ? 'text-[#10b981]' : 'text-slate-500'">check_circle</span>
                            <span class="text-sm text-slate-300">{{ $t('ourPlans.rules.basicTaskManagemnt') }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined"
                                :class="activePlan === 'free' ? 'text-red-500 opacity-45' : 'text-blue-500'">unpublished</span>
                            <span class="text-sm text-slate-300 opacity-45">{{ $t('ourPlans.rules.openTeamChat')
                                }}</span>
                        </div>
                    </div>
                    <button class="w-full py-4 px-6 rounded-2xl border font-bold"
                        :class="activePlan === 'free' ? 'current-plan' : 'another-plan'"
                        @click="handleUpgradePlan('free')">
                        {{ activePlan === 'free' ? $t('ourPlans.planActive') : $t('ourPlans.getStarted') }}
                    </button>
                </div>
                <div class="glass-card rounded-[2rem] p-10 flex flex-col relative"
                    :class="activePlan === 'pro' && 'pro-glow'">
                    <div class="absolute -top-4 right-10 bg-[#10b981] text-black px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest shadow-[0_0_20px_rgba(16,185,129,0.4)]"
                        v-if="activePlan === 'pro'">
                        Current Plan
                    </div>
                    <div class="mb-10">
                        <div class="inline-block px-3 py-1 rounded-md  text-xs font-bold uppercase tracking-widest mb-4"
                            :class="activePlan === 'pro' ? 'bg-[#10b981]/10 text-[#10b981]' : ' bg-white/5 text-blue-500'">
                            Professional</div>
                        <h3 class="text-white font-bold text-2xl mb-2">Pro</h3>
                        <div class="flex items-baseline gap-1 mb-4">
                            <span class="text-5xl font-black text-white">$29.99</span>
                            <span class="text-slate-400 font-medium">/mo</span>
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed">Unlock advanced collaboration tools and
                            unlimited flexibility for your growing business.</p>
                    </div>
                    <div class="space-y-6 mb-12 flex-1">
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined"
                                :class="activePlan === 'pro' ? 'text-[#10b981]' : 'text-blue-500'">check_circle</span>
                            <span class="text-sm text-slate-300">{{ $t('ourPlans.rules.maxCreated') }}: <span
                                    class="text-white font-bold">{{ $t('ourPlans.unlimited') }}</span></span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined"
                                :class="activePlan === 'pro' ? 'text-[#10b981]' : 'text-blue-500'">check_circle</span>
                            <span class="text-sm text-slate-300">{{ $t('ourPlans.rules.maxJoined') }}: <span
                                    class="text-white font-bold">{{ $t('ourPlans.unlimited') }}</span></span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined"
                                :class="activePlan === 'pro' ? 'text-[#10b981]' : 'text-blue-500'">check_circle</span>
                            <span class="text-sm text-slate-300">{{ $t('ourPlans.rules.basicTaskManagemnt') }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined"
                                :class="activePlan === 'pro' ? 'text-[#10b981]' : 'text-blue-500'">check_circle</span>
                            <span class="text-sm text-slate-300 opacity-45">{{ $t('ourPlans.rules.openTeamChat')
                                }}</span>
                        </div>

                    </div>
                    <button class="w-full py-4 px-6 rounded-2xl border font-bold text-center"
                        :class="activePlan === 'pro' ? 'current-plan' : 'another-plan'"
                        @click="handleUpgradePlan('pro')">
                        {{ activePlan === 'pro' ? $t('ourPlans.planActive') : $t('ourPlans.getStarted') }}
                    </button>
                </div>
            </div>
            <div class="mt-32 pt-16 border-t border-white/5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center md:text-left">
                    <div>
                        <h4 class="text-white font-bold mb-3 text-lg">{{ $t('ourPlans.safeAndSecureTitle') }}</h4>
                        <p class="text-slate-500 text-xs sm:text-sm">{{ $t('ourPlans.safeAndSecureDec') }}</p>
                    </div>
                    <div>
                        <h4 class="text-white font-bold mb-3 text-lg">{{ $t('ourPlans.scaleWithEaseTitle') }}</h4>
                        <p class="text-slate-500 text-xs sm:text-sm">{{ $t('ourPlans.scaleWithEaseDec') }}</p>
                    </div>
                    <div>
                        <h4 class="text-white font-bold mb-3 text-lg">{{ $t('ourPlans.supportTitle') }}</h4>
                        <p class="text-slate-500 text-xs sm:text-sm">{{ $t('ourPlans.supportDec') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style type="text/tailwindcss">
body {
    font-family: 'Inter', sans-serif;
    background-color: #111418;
}

.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.glass-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.pro-glow {
    box-shadow: 0 0 40px -10px rgba(16, 185, 129, 0.3);
    border: 1px solid rgba(16, 185, 129, 0.5);
}

.pricing-grid {
    max-width: 900px;
}
</style>