<script setup>
import { useUserStore } from '@/stores/user';
import { RouterLink, useRouter } from 'vue-router';
const userStore = useUserStore();
const router = useRouter()
const activePlan = userStore.userPlan
</script>
<template>
    <aside class="w-20 md:w-64 flex flex-col bg-[#0f1117] border-r border-[#30363d] h-screen"
        :dir="$i18n.locale === 'ar' ? 'rtl' : 'ltr'">
        <!-- Header -->
        <div class="p-[0.94rem] flex items-center justify-center md:justify-start  gap-3 border-b border-[#30363d]">
            <div class="bg-blue-600 p-2 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-white" style="font-size: 20px;">hub</span>
            </div>
            <h1 class="text-lg font-bold tracking-tight text-white hidden md:block">TeamHub</h1>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            <!-- Dashboard -->
            <router-link :to="{ name: 'dashboard' }"
                class="flex items-center md:gap-3 gap-0 md:px-3 px-0 py-2.5 rounded-md text-[#8b949e] hover:text-white hover:bg-[#30363d] cursor-pointer transition-colors md:justify-start justify-center">
                <span class="material-symbols-outlined text-base">dashboard</span>
                <!-- <p class="text-sm font-medium hidden md:block">Dashboard</p> -->
                <p class="text-sm font-medium hidden md:block">{{ $t('side_nav.dashboard') }}</p>
            </router-link>

            <!-- My Teams -->
            <router-link :to="{ name: 'my-teams' }"
                class="flex items-center md:gap-3 gap-0 md:px-3 px-0 py-2.5 rounded-md text-[#8b949e] hover:text-white hover:bg-[#30363d] cursor-pointer transition-colors md:justify-start justify-center">
                <span class="material-symbols-outlined">groups</span>
                <p class="text-sm font-medium hidden md:block">{{ $t('side_nav.myTeams') }}</p>
            </router-link>

            <!-- Notifications -->
            <router-link :to="{ name: 'notifications' }"
                class="flex items-center md:gap-3 gap-0 md:px-3 px-0 py-2.5 rounded-md text-[#8b949e] hover:text-white hover:bg-[#30363d] cursor-pointer transition-colors md:justify-start justify-center">
                <span class="material-symbols-outlined text-base">notifications</span>
                <p class="text-sm font-medium hidden md:block">{{ $t('side_nav.notifications') }}</p>
            </router-link>

            <!-- Our Plans -->
            <router-link :to="{ name: 'our-plans' }" v-if="userStore.userPlan !== 'pro'"
                class="flex items-center md:gap-3 gap-0 md:px-3 px-0 py-2.5 rounded-md text-[#8b949e] hover:text-white hover:bg-[#30363d] cursor-pointer transition-colors md:justify-start justify-center">
                <span class="material-symbols-outlined">bookmark_stacks</span>
                <p class="text-sm font-medium hidden md:block">{{ $t('side_nav.ourPlans') }}</p>
            </router-link>

            <!-- Settings -->
            <router-link :to="{ name: 'settings' }"
                class="flex items-center md:gap-3 gap-0 md:px-3 px-0 py-2.5 rounded-md text-[#8b949e] hover:text-white hover:bg-[#30363d] cursor-pointer transition-colors md:justify-start justify-center">
                <span class="material-symbols-outlined text-base">settings</span>
                <p class="text-sm font-medium hidden md:block">{{ $t('side_nav.settings') }}</p>
            </router-link>
        </nav>

        <!-- Profile Section -->
        <div class="p-4 border-t border-[#30363d]">
            <div class="flex items-center md:gap-3 gap-0 px-2 py-3 rounded-md hover:bg-[#30363d] cursor-pointer transition-colors md:flex-row flex-col"
                @click="router.push({ name: 'settings' })">
                <img v-if="userStore.userAvatar" :src="userStore.userAvatar" alt="User Avatar"
                    class="h-10 w-10 rounded-full bg-cover bg-center flex-shrink-0">

                <div class="h-10 w-10 rounded-full bg-cover bg-center flex-shrink-0" v-else
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBr-ptJI6EmHcLFN4Ro5pLDaj9cI_i8x1T59sYxV1d9t77EXfhV4xUg-Kh3m9YUkBpwxb5Etkher7-Js3H0pMYXgTYmo0om0wh-1TX6kqUUsW6a2BHwsJDrE2zacu_Y0K5sbQvZMWTViX08Q4NbSzCkYOgWhsZyqGwvldmYydi3oc-Lo1OU7o8hiayIQwemghvWZ669ySe6V5xY-udMOInryiMN_zTrIm1DgtYbJp6Xv09btZrpi8NptpS5wXGdubskIFX6wODdTGs');">
                </div>

                <div class="md:flex flex-col flex-1 min-w-0 hidden">
                    <h2 class="text-sm font-semibold text-white truncate">{{ userStore.userName }}</h2>
                    <p class="text-xs text-[#8b949e]">{{ userStore.userPlan }}</p>
                </div>
            </div>

            <!-- Upgrade Plan Button -->
            <router-link :to="{ name: 'settings' }"
                class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-md transition-colors cursor-pointer hidden md:block text-center"
                v-if="activePlan === 'free'">
                Upgrade Plan
            </router-link>
        </div>
    </aside>
</template>