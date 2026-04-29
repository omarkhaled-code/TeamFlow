<template>

    <div class="w-full lg:w-1/2 bg-gray-800 flex flex-col justify-center px-8 sm:px-12 py-12">
        <div class="w-full max-w-md mx-auto">
            <div class="mb-8">
                <h2 class="text-4xl font-bold text-white mb-2">{{ $t('register.getStarted') }}</h2>
                <p class="text-gray-400">
                    {{ $t("register.haveAccount") }}
                    <a href="#" @click.prevent="switchToLogin" class="text-blue-500 hover:text-blue-400">{{
                        $t('register.loginWithEmail') }}</a>
                </p>
            </div>
            <!-- Register Form -->
            <form @submit.prevent="handleRegister" class="space-y-6">
                <div>
                    <label for="full-name" class="block text-sm font-medium text-white mb-2">
                        {{ $t("register.userName") }}
                    </label>
                    <input id="full-name" v-model="fullName" type="text" placeholder="John Doe"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none transition"
                        required />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">
                        {{ $t("register.userEmail") }}
                    </label>
                    <input id="email" v-model="mail" type="email" placeholder="name@company.com"
                        class="w-full px-4 py-3 bg-gray-700 border  rounded-lg text-white placeholder-gray-400  focus:outline-none transition"
                        :class="errors.email ? 'border-red-500' : 'border-gray-600 focus:border-blue-500'"
                        @input="errors.email = null" required />
                    <p v-if="errors.email" class="text-red-500 text-sm">{{ errors.email[0] }}</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-white">
                            {{ $t('register.userPass') }}
                        </label>
                    </div>
                    <div class="relative">
                        <input id="password" v-model="pass" :type="passwordVisible ? 'text' : 'password'"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 pr-12 bg-gray-700 border  rounded-lg text-white placeholder-gray-400 focus:outline-none transition"
                            :class="errors.password ? 'border-red-500' : 'border-gray-600 focus:border-blue-500'"
                            @input="errors.password = null" required />
                        <p v-if="errors.password" class="text-red-500 text-sm">{{ errors.password[0] }}</p>
                        <button type="button" @click="passwordVisible = !passwordVisible"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300">
                            <svg v-if="passwordVisible" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21">
                                </path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="confirm-password" class="block text-sm font-medium text-white">
                            {{ $t('register.confirmPass') }}
                        </label>
                    </div>
                    <div class="relative">
                        <input id="confirm-password" v-model="confirmPassword"
                            :type="confirmPasswordVisible ? 'text' : 'password'" placeholder="••••••••"
                            class="w-full px-4 py-3 pr-12 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none transition"
                            required />
                        <button type="button" @click="confirmPasswordVisible = !confirmPasswordVisible"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300">
                            <svg v-if="confirmPasswordVisible" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21">
                                </path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition duration-200 mt-8 cursor-pointer">
                    {{ $t("register.createPassBtn") }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
const emit = defineEmits(['switch-to-login'])
const router = useRouter()

const fullName = ref('')
const mail = ref('')
const pass = ref('')
const confirmPassword = ref('')
const passwordVisible = ref(false)
const confirmPasswordVisible = ref(false)

const errors = ref({
    email: "",
    password: ""
})

const handleRegister = async () => {
    // Handle register logic here
    if (pass.value !== confirmPassword.value) {
        alert("Passwords do not match!");
        return;
    }
    const res = await useUserStore().register({
        name: fullName.value,
        email: mail.value,
        password: pass.value
    });
    if (res.success) {
        return router.push('/');
    }

    // error.value = res.error.errors


    let { email, password } = res.error.errors
    email && (errors.value.email = email)
    password && (errors.value.password = password)

}

const switchToLogin = () => {
    emit('switch-to-login')
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
