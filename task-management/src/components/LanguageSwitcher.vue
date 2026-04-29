<template>
    <div class="relative">
        <button @click="toggleDropdown"
            class="flex items-center gap-2 px-3 py-2 rounded-md bg-[#30363d] hover:bg-[#3d4451] text-white transition-colors">
            <span class="material-symbols-outlined text-base">language</span>
            <span class="text-sm font-medium">{{ currentLanguage }}</span>
            <span class="material-symbols-outlined text-base">
                {{ isOpen ? 'expand_less' : 'expand_more' }}
            </span>
        </button>

        <!-- Dropdown -->
        <div v-if="isOpen"
            class="absolute top-full mt-2 right-0 bg-[#1a1f2e] border border-[#30363d] rounded-lg shadow-lg overflow-hidden min-w-[150px] z-50">
            <button @click="changeLanguage('en')"
                class="w-full px-4 py-2.5 text-left hover:bg-[#30363d] transition-colors flex items-center gap-3"
                :class="{ 'bg-[#30363d]': $i18n.locale === 'en' }">
                <span class="text-2xl">🇺🇸</span>
                <span class="text-sm text-white">English</span>
            </button>
            <button @click="changeLanguage('ar')"
                class="w-full px-4 py-2.5 text-left hover:bg-[#30363d] transition-colors flex items-center gap-3"
                :class="{ 'bg-[#30363d]': $i18n.locale === 'ar' }">
                <span class="text-2xl">🇸🇦</span>
                <span class="text-sm text-white">العربية</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
const isOpen = ref(false)

const currentLanguage = computed(() => {
    return locale.value === 'ar' ? 'العربية' : 'English'
})

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
}

const changeLanguage = (lang) => {
    locale.value = lang
    localStorage.setItem('language', lang)
    document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr')
    document.documentElement.setAttribute('lang', lang)
    isOpen.value = false
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        isOpen.value = false
    }
}

if (typeof window !== 'undefined') {
    window.addEventListener('click', handleClickOutside)
}
</script>