<script setup lang="ts">
import NavBar from '@/components/NavBar.vue';
import router from '@/router';

</script>

<template>
    <NavBar />
    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
        <div class="relative mb-8">
            <div class="text-[180px] font-black leading-none text-slate-200 dark:text-[#1c2229] select-none">
                404
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span
                    class="material-symbols-outlined text-[100px] text-primary opacity-80 animate-pulse">explore_off</span>
            </div>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Whoops! This page moved to a
            different team.</h1>
        <p class="text-slate-500 dark:text-[#9dabb9] text-lg max-w-lg mb-10 leading-relaxed">
            We couldn't find the page you're looking for. It might have been deleted or the URL is incorrect.
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
            <router-link :to="{ name: 'dashboard' }"
                class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-primary/20"
                href="#">
                <span class="material-symbols-outlined text-xl">dashboard</span>
                Back to Dashboard
            </router-link>

        </div>

    </div>
</template>