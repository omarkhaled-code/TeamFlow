<script setup lang="ts">
import NavBar from '@/components/NavBar.vue';
import { useUserStore } from '@/stores/user';
import api from '@/utils/api';
import { onMounted } from 'vue';

const userStore = useUserStore();
onMounted(() => {
    userStore.fetchUserData()
})

const handleDeleteUser = async () => {
    if (!confirm("Are You Sure Want to Delete This Account")) return;
    await api.delete('/user');
    userStore.fetchUserData()
}

</script>

<template>
    <div class="min-h-screen ">
        <NavBar :title="$t('nav.settingsTitle')" :description="$t('nav.settingsDec')" />
        <main class="flex-1 flex flex-col overflow-y-auto">

            <div class="p-8 max-w-4xl mx-auto w-full">
                <section class="mb-10">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold">{{ $t('settingsPage.title') }}</h3>
                    </div>
                    <div class="bg-[#1a232e] border border-[#283039] rounded-xl overflow-hidden">
                        <div class="p-6 border-b border-[#283039] flex items-center justify-between">
                            <div class="flex justify-between w-full">
                                <div class="flex items-center gap-6">
                                    <div class="relative group">
                                        <img class="h-15 w-15 sm:h-20 sm:w-20 rounded-full bg-cover bg-center border-2 border-[#1F548F]"
                                            :src="`${userStore.userAvatar}?t=${Date.now()}`" alt="usre avatar">
                                    </div>
                                    <div class="hidden sm:block">
                                        <h4 class="text-lg font-bold">{{ $t('settingsPage.profilePhoto') }}</h4>
                                        <p class="text-sm text-[#9dabb9]">{{ $t('settingsPage.updateDec') }}</p>
                                    </div>
                                </div>
                                <router-link :to="{ name: 'update-user' }"
                                    class="hover:text-blue-500 cursor-pointer text-4xl duration-200">
                                    <span class="material-symbols-outlined">
                                        edit_square
                                    </span>
                                </router-link>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex items-center justify-between">
                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-[#9dabb9] uppercase tracking-wider">
                                        {{ $t('settingsPage.fullName') }}</p>
                                    <p class="text-xs sm:text-sm font-medium">{{ userStore.user?.name }}</p>
                                </div>

                            </div>
                            <div class="flex items-center justify-between">
                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-[#9dabb9] uppercase tracking-wider">
                                        {{ $t("settingsPage.email") }}
                                    </p>
                                    <p class="text-xs sm:text-sm font-medium">{{ userStore.user?.email }}</p>
                                </div>

                            </div>

                        </div>
                    </div>
                </section>


                <section class="mt-12 pt-12 border-t border-[#283039]">
                    <div
                        class="flex items-center justify-between p-6 bg-red-500/5 border border-red-500/20 rounded-xl flex-col sm:flex-row gap-4">
                        <div>
                            <h4 class="font-bold text-white text-xs sm:text-red-400">{{ $t('settingsPage.deleteTitle')
                            }}
                            </h4>
                            <p class="hidden sm:block text-xs text-[#9dabb9]">{{ $t("settingsPage.deleteDec") }}</p>
                        </div>
                        <button
                            class="px-4 py-2 border border-red-500/50 text-red-400 hover:bg-red-500 hover:text-white text-sm font-bold rounded-lg cursor-pointer transition-all"
                            @click="handleDeleteUser">
                            {{ $t('settingsPage.deleteBtn') }}
                        </button>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>