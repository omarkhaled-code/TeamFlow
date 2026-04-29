<script setup>
import { RouterView } from 'vue-router'
import SideNav from '@/components/SideNav.vue';
import { useUserStore } from '@/stores/user';
import { watch } from 'vue'
import { useI18n } from 'vue-i18n'

const userStore = useUserStore();

const { locale } = useI18n()

// Set initial direction
document.documentElement.setAttribute('dir', locale.value === 'ar' ? 'rtl' : 'ltr')
document.documentElement.setAttribute('lang', locale.value)

// Watch for language changes
watch(locale, (newLocale) => {
  document.documentElement.setAttribute('dir', newLocale === 'ar' ? 'rtl' : 'ltr')
  document.documentElement.setAttribute('lang', newLocale)
})



</script>

<template>
  <main class="bg-background-light dark:bg-[#101922] text-white font-display bg-[#101922]"
    :dir="$i18n.locale === 'ar' ? 'rtl' : 'ltr'">
    <link rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <div class="flex h-screen overflow-hidden">
      <SideNav v-if="userStore.token" class="z-10" />
      <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- Top Navigation Bar -->
        <!-- <header
          class="flex items-center justify-between border-b border-[#283039] px-8 py-4 bg-backgroundbg-[#101922]-background-dark sticky top-0 z-10bg-[#101922] bg-[#101922]">
          <div class="flex items-center gap-4 flex-1 max-w-xl">
            <label class="relative w-full">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#9dabb9]">
                <span class="material-symbols-outlined">search</span>
              </span>
              <input
                class="w-full bg-[#283039] border-none text-sm rounded-lg pl-10 pr-4 py-2 focus:ring-1 focus:ring-primary placeholder-[#9dabb9]"
                placeholder="Search teams or projects..." type="text" />
            </label>
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
        </header> -->
        <!-- Dashboard Content -->
        <RouterView />
      </div>
    </div>
  </main>
</template>