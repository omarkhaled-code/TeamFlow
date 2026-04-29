<script setup lang="ts">
import { defineAsyncComponent } from 'vue';

const LoginForm = defineAsyncComponent(() => import('./Login.vue'));
const RegisterForm = defineAsyncComponent(() => import('./Register.vue'));

import { ref } from 'vue';

const registerationStatus = ref<'login' | 'register'>('login');

const switchToRegister = () => {
    registerationStatus.value = 'register';
}

const switchToLogin = () => {
    registerationStatus.value = 'login';
}
</script>

<template>
    <div class="min-h-screen bg-gray-900 flex">
        <!-- Left Section -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-700 to-blue-400 flex-col justify-between p-12">
            <div>
                <div class="flex items-center gap-2 mb-16">
                    <div class="bg-white rounded-lg p-2">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" />
                        </svg>
                    </div>
                    <span class="text-white text-2xl font-bold">TaskSync</span>
                </div>
            </div>

            <div>
                <h1 class="text-5xl font-bold text-white mb-6 leading-tight">
                    {{ $t('register.welcomePage.title') }}<br />
                    {{ $t('register.welcomePage.subTitle') }}
                </h1>
                <p class="text-blue-100 text-lg mb-12">
                    {{ $t('register.welcomePage.dec') }}
                </p>
            </div>

            <div class="bg-amber-100 rounded-2xl p-8 shadow-xl">
                <div class="bg-white rounded-lg p-4 inline-block">
                    <p class="text-gray-800 font-semibold text-sm mb-4">TEAM TASKS</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <div class="h-2 bg-gray-300 w-32 rounded"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div class="h-2 bg-gray-300 w-40 rounded"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                            <div class="h-2 bg-gray-300 w-36 rounded"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <div class="h-2 bg-gray-300 w-32 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right Section -->
        <LoginForm v-if="registerationStatus == 'login'" @switch-to-register="switchToRegister" />
        <RegisterForm v-else @switch-to-login="switchToLogin" />
    </div>
</template>

<style scoped>
.registeration-side {
    width: 50%;
    height: 100vh;
    padding: 2rem;
    background-color: #147FEC;
    display: flex;
    justify-content: space-between;
    flex-direction: column;

    .header {
        display: flex;
        align-items: center;
        gap: 10px;

        img {
            width: 50px;
            height: 50px;
        }
    }

    .img {
        width: 90%;
        height: 45%;

        img {
            width: 100%;
            height: 100%;
            border-radius: 1rem;
            object-fit: cover;
            object-position: center;
        }
    }
}
</style>