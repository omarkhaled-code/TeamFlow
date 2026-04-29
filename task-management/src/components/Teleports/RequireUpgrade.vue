<script setup lang="ts">

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits("update:show")
const close = () => emit("update:show", false);


</script>

<template>

    <!-- Centered Modal Component -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-6">
        <!-- overlay -->
        <div class="absolute inset-0 bg-background-dark/80 backdrop-blur-2xl" @click.self="close"></div>

        <div
            class="relative w-full max-w-[560px] bg-[#1A1E23] rounded-2xl modal-glow overflow-hidden border border-white/10 p-8 md:p-12 text-center">
            <div class="flex justify-center mb-6">
                <div
                    class="size-16 bg-[#137fec]/15 rounded-full flex items-center justify-center text-[#137fec] border border-[#137fec]/20">
                    <span class="material-symbols-outlined !text-4xl">layers</span>
                </div>
            </div>
            <div class="flex flex-col gap-2 mb-8">
                <h1 class="text-white text-3xl font-bold leading-tight tracking-tight">
                    You've reached your team limit
                </h1>
                <p class="text-gray-400 text-lg font-normal leading-relaxed">
                    You’ve done a great job growing your organization! To keep adding new teams and collaborators,
                    consider moving to our Pro plan.
                </p>
            </div>
            <div class="glass-card rounded-xl p-6 mb-8 text-left">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-white font-bold text-lg">Pro Plan Benefits</h3>
                    <span
                        class="bg-[#137fec] text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Recommended</span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 size-6 bg-[#137fec] rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[16px]">check</span>
                        </div>
                        <p class="text-gray-200 text-base font-medium">Create Unlimited teams</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 size-6 bg-[#137fec] rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[16px]">check</span>
                        </div>
                        <p class="text-gray-200 text-base font-medium">Join To Unlimited teams</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 size-6 bg-[#137fec] rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[16px]">check</span>
                        </div>
                        <p class="text-gray-200 text-base font-medium">Open Team Chats</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-3">
                <router-link :to="{ name: 'checkout' }"
                    class="w-full h-12 bg-[#137fec] hover:bg-[#0e64bb] text-white rounded-xl font-bold text-base transition-all flex items-center justify-center gap-2 active:scale-[0.98] cursor-pointer">
                    <span class="truncate">Upgrade Now</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </router-link>
                <button
                    class="w-full h-12 bg-transparent hover:bg-white/5 text-gray-400 hover:text-white rounded-xl font-medium text-base transition-colors cursor-pointer"
                    @click="close">
                    Maybe Later
                </button>
            </div>
            <p class="mt-8 text-gray-500 text-[.8rem] uppercase tracking-[0.2em] font-bold ">
                Starting at <span class="text-[#137fec]">$29.99</span>/month

            </p>
        </div>
    </div>


    <!--  -->

</template>

<style type="text/tailwindcss">
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.modal-glow {
    box-shadow: 0 0 40px rgba(19, 127, 236, 0.08);
}

.glass-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.08);
}
</style>