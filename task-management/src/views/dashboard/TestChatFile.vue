<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import api from '@/utils/api';
import { useUserStore } from '@/stores/user'; // your Pinia store
// import Echo from '@/echo';

const userStore = useUserStore();
const globalChatMessages = ref([]);
const privateChatMessages = ref([])
const globalChatId = ref([]);
const newMessage = ref('');
const teamId = ref(1); // replace with dynamic team id

let channel: any = null;

onMounted(async () => {
    // 1️⃣ Fetch previous messages
    const res = await api.get(`/teams/${teamId.value}/messages`);
    // messages.value = res.data.data;
    console.log(res.data.data.global_chat.messages);
    globalChatMessages.value = res.data.data.global_chat.messages
    privateChatMessages.value = res.data.data.private_chats.messages
    globalChatId.value = res.data.data.global_chat.id;

    // 
    // {
    //     "data": {
    //         "global_chat": {
    //             "id": 1,
    //                 "team_id": 1,
    //                     "type": "team",
    //                         "created_at": "2026-02-03T12:27:21.000000Z",
    //                             "updated_at": "2026-02-03T12:27:21.000000Z",
    //                                 "messages": []
    //         },
    //         "private_chats": []
    //     }
    // }
    // 


    // 2️⃣ Subscribe to private channel
    channel = window.Echo.private(`team.${teamId.value}`)
        .listen('MessageSent', (e: any) => {
            globalChatMessages.value.push(e.message);
        });
});

onBeforeUnmount(() => {
    if (channel) channel.stopListening('MessageSent');
});

// 3️⃣ Send message
async function sendMessage() {
    if (!newMessage.value.trim()) return;

    await api.post(`/teams/${globalChatId.value}/messages`, {
        content: newMessage.value,
        chat_id: globalChatId.value
    });

    newMessage.value = '';
}
</script>

<template>
    <div class="flex flex-1 overflow-hidden">
        <!-- Side Messages display members -->
        <aside class="w-80 flex flex-col bg-[#111418] border-r border-white/5 h-full">
            <div class="p-6">
                <h3 class="text-[11px] font-bold uppercase tracking-[1.5px] text-[#5a6b7a] mb-4">Channels</h3>
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/5 border border-white/5 cursor-pointer group hover:border-white/10 transition-all">
                        <div
                            class="w-8 h-8 rounded-lg bg-[#137fec]/20 flex items-center justify-center text-[#137fec] group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-lg">public</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">Global Room</p>
                            <p class="text-[10px] text-[#9dabb9]">24 members online</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto px-6 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[11px] font-bold uppercase tracking-[1.5px] text-[#5a6b7a]">Direct Messages</h3>
                    <button class="text-[#5a6b7a] hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-lg">add_circle</span>
                    </button>
                </div>
                <div class="space-y-1">
                    <div
                        class="flex items-center gap-3 px-3 py-3 rounded-xl bg-[#137fec]/10 border border-[#137fec]/20 cursor-pointer">
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full bg-center bg-cover"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAe3Jcq0sligitjXyc2wNHgN0NLQrsfGQ8vjcjxWXYS0ThFVRTeeFUWUbAWT8ypE1cZ-OH5a_Ju7ofSkhcW7YZBj5GQ00IdP7IRZLLQ4rwaPgixGIrvFIgwwiR1-aX9aj0O9I3ZMUN9NO58b-39KSduNcYOz7gMBty8lG6bwhZZmthvA5VhE338uSCibHs50Sr63VPFX6Ea8zAeOWAYvVTEQaxXZ4oP98BZHGQwo4cyptk_5T0QX126zfxtG3fXgLRNcsp77aPyRY4')">
                            </div>
                            <div
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-[#111418] status-glow">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-white truncate">Alex Rivera</p>
                            <p class="text-[11px] text-[#137fec] font-medium truncate">Typing...</p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 transition-all cursor-pointer group">
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full bg-center bg-cover"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD1YZcd37ksE8bYKPjYUbFNAlh0bVRrFdRkbr_p2tZlEDheK43FBcV-R6lg6reUB-LlzrWKKV6NQEPUxuK-kCEgCCzyeah1nlM-zF_lHyEqb-gwlGqDVXMRXXFez4E42Cj09mLXPkbBFr8cjmVly8J8HqZeRuMzDcLEZVKNaYX5VdpEBZz4Hp7QEVs5L3btKewIwbKtIvhRiuAaenekh0yMaPI7-Fcu7ub3rX33ltcOdsMHmSgj-zW9pMM1iJlgir5J2QcHabEthoU')">
                            </div>
                            <div
                                class="absolute bottom-0 right-0 w-3 h-3 bg-zinc-600 rounded-full border-2 border-[#111418]">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-medium text-[#9dabb9] group-hover:text-white transition-colors truncate">
                                Sarah Chen</p>
                            <p class="text-[11px] text-[#5a6b7a] truncate">Offline</p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 transition-all cursor-pointer group">
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full bg-center bg-cover"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCpg4IfVnnp71RZ0_V6XAw-Tx-FBZjck77f0YNKuPGIaMd_XSPlO-dQE5nTtozAaXFzaPTKv9Ic3kH0dz3eV92ATrUp7_jRrUoATw69Wr307DcwH1CoDkO0YIEqpCBqXG2-ILBXXJTRv2OzGnoaqMQ_soAyBdnmmYlZ46wXDcfiOGavs9fqOaVV1_ifDxKq3j6hn4Pq6eBzQa7Pj9NbEvb1mehb2Gq7EwSk3iQ9hxx3ie41vEMIGv3OPAaupBhc8JTq11Dwxtooq4o')">
                            </div>
                            <div
                                class="absolute bottom-0 right-0 w-3 h-3 bg-amber-500 rounded-full border-2 border-[#111418]">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-medium text-[#9dabb9] group-hover:text-white transition-colors truncate">
                                Marcus Brown</p>
                            <p class="text-[11px] text-[#5a6b7a] truncate">Away</p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 transition-all cursor-pointer group">
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full bg-center bg-cover"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAYYpIgq93wNJoP2rlyKPgsj8ffN-g67inAQC2ElavhX_tO7GovbYhlaDl2DckwmJFTCRXetjXjvlZNtuHc-iaWnGuAz6UWq_deENNA4MW9O_98GdD7Yiss6wkgdz24mgdL62QOmN1fSQy0Wt7UkT3ivjw-tsitB4zZsyzI311v9cbHWMxY044Xr3kSUN7xxnoFiBKSHDwCh6MiI1fgLhPt6zhiyRts5f6lCo5doDuXmBZ-42Tsyv7JADlw2L6SNscxm2QyGIrPj9I')">
                            </div>
                            <div
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-[#111418] status-glow">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-medium text-[#9dabb9] group-hover:text-white transition-colors truncate">
                                Elena Rodriguez</p>
                            <p class="text-[11px] text-[#5a6b7a] truncate">Active 5m ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Messge Box -->
        <main class="flex-1 flex flex-col bg-[#0b0f14] relative overflow-hidden">
            <!-- Messages Box Header -->
            <header
                class="h-16 border-b border-white/5 flex items-center justify-between px-8 bg-[#111418]/50 backdrop-blur-xl z-10">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-xl bg-center bg-cover border border-white/10"
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAe3Jcq0sligitjXyc2wNHgN0NLQrsfGQ8vjcjxWXYS0ThFVRTeeFUWUbAWT8ypE1cZ-OH5a_Ju7ofSkhcW7YZBj5GQ00IdP7IRZLLQ4rwaPgixGIrvFIgwwiR1-aX9aj0O9I3ZMUN9NO58b-39KSduNcYOz7gMBty8lG6bwhZZmthvA5VhE338uSCibHs50Sr63VPFX6Ea8zAeOWAYvVTEQaxXZ4oP98BZHGQwo4cyptk_5T0QX126zfxtG3fXgLRNcsp77aPyRY4')">
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-[#111418] status-glow">
                        </div>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">Alex Rivera</h2>
                        <p class="text-[11px] text-[#9dabb9] font-medium uppercase tracking-wide">Product Designer •
                            Online</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="p-2.5 hover:bg-white/5 rounded-xl text-[#9dabb9] transition-colors">
                        <span class="material-symbols-outlined">call</span>
                    </button>
                    <button class="p-2.5 hover:bg-white/5 rounded-xl text-[#9dabb9] transition-colors">
                        <span class="material-symbols-outlined">videocam</span>
                    </button>
                    <div class="w-[1px] h-6 bg-white/10 mx-2"></div>
                    <button class="p-2.5 hover:bg-white/5 rounded-xl text-[#9dabb9] transition-colors">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                    <button class="p-2.5 hover:bg-white/5 rounded-xl text-[#9dabb9] transition-colors">
                        <span class="material-symbols-outlined">more_vert</span>
                    </button>
                </div>
            </header>
            <!-- Messges Content -->
            <div class="flex-1 overflow-y-auto p-8 space-y-8 ">
                <div class="relative flex items-center justify-center">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/5"></div>
                    </div>
                    <span
                        class="relative bg-[#0b0f14] px-6 text-[10px] font-bold text-[#5a6b7a] uppercase tracking-[3px]">Today,
                        October 23</span>
                </div>
                <div class="flex gap-4 max-w-4xl group">
                    <div class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCPMt7skim4twXEETBbM5r_KgFH5v3LDJIXK2AcJgGIrogMArHMulqGiS8LJlzHhIGcDYjfC9QH6HATU-0MhlIrBTgcWV1SFWL6A3mJ8UEM1ehloxMKRBueF3VFZl8XpqMR5_AcYTFDbJcxs7M3zqkCI4Ek8fWvKurfj_1J9-kK0N03EoeN194px1gSer3n-Lgi2TRio3qJ9TT0bGvG21qzfaZL0Mrl-Kl5EBJVZTiBGxWbyDeILiFGU1Om9RQ2RCMSCJHwT85kPhE')">
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white">Alex Rivera</span>
                            <span class="text-[10px] text-[#5a6b7a]">11:20 AM</span>
                        </div>
                        <div
                            class="bg-[#1c2127] border border-white/5 px-4 py-3 rounded-2xl rounded-tl-none text-sm leading-relaxed text-[#d1d5db] shadow-sm">
                            Hey team! I've just uploaded the final prototypes for the messenger overhaul. Can you check
                            the typography on the mobile view?
                        </div>
                    </div>
                </div>
                <div class="flex gap-4 max-w-4xl">
                    <div class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuC8BLzdTyfMBBa5d7MrozaRYxFmZtc0NaA2-guXMRvJxDpGYFK7I8rZRtysqyUoa0AQsWw7kx13CwuBbRUKat40M0Do60WTHWIKFUxrsn8gLEbRKKfDavTKfJ2FZq2nBuKAW_CGuUc3nkskr90N1X7d86fmCWn8u4iwDZfJI_v_DFqQTz9LZZs0-PDXw8t5fCLOnhR1sQWnGoZhpLmz-uO1JEeBNGbv5uUMhUCKw0KRI2Jm4vo3UgJDcc6HAopHttmrl0fdFv9vf6Y')">
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white">Alex Rivera</span>
                            <span class="text-[10px] text-[#5a6b7a]">11:21 AM</span>
                        </div>
                        <div class="glass-panel p-2 rounded-2xl overflow-hidden max-w-md shadow-2xl">
                            <div class="rounded-xl h-56 bg-center bg-cover border border-white/10"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD7lw8oCuqo21WXsu3-LMR4v4VfEymai5nlkjasS_7_ReySZ79Qy6PnDxfDB9hj_I78rDuRxVtIQkiDUDuvi6cehT0fw6A5niHY7L8rb700aCIKfMOsaJVBALDdTuZHEEhKNGOoMpVYsJ7asXBWotQfadACS095tWxM4ctjYlQFz1rZRm0-J9saOwKncy4DemirIM8h45s-EaTp5i69-VEeqnVvAoi5m6RKkMDvsg9BB8KJbigqMijcqJi5Q5X3CH2uxhaFNtKWmiM')">
                            </div>
                            <div class="p-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[#137fec] text-sm">image</span>
                                    <p class="text-xs font-medium text-[#9dabb9]">mobile_redesign_v2.png (2.4 MB)</p>
                                </div>
                                <span
                                    class="material-symbols-outlined text-sm text-[#137fec] cursor-pointer hover:text-white transition-colors">download</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-4 max-w-4xl ml-auto flex-row-reverse">
                    <div class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBYQJ5i55tXK1sv8JnXVJgK_-V4zflJnKpnANFTh0Z6EjWpl1d8TwSa1hGUNdLj5yJtRinXKNpUS38pAzEF5WkbfTLUWi3KPEpyX94viHYCMytRUHe7ixz-__HPePmbFmAcnRJD6fVl2WCVE6V_Kt-5Pv5viF7KLjfmipeJHhmOem4Jr_gxrjgoqvmLiHSxTrlLpAdwYUNlAGS7sFLzp-y1YunMwLcC4I4LUij3yBaGgWXx8p55tZvSTu-JGY8RpvsNwuJ3zzu_b1E')">
                    </div>
                    <div class="space-y-1.5 items-end flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-[#5a6b7a]">11:25 AM</span>
                            <span class="text-sm font-bold text-white">Jordan Smith (Me)</span>
                        </div>
                        <div
                            class="bg-[#137fec] px-4 py-3 rounded-2xl rounded-tr-none text-sm leading-relaxed text-white shadow-lg shadow-[#137fec]/10">
                            Looking great Alex! The spacing seems much better now. I'll review the dev handoff docs this
                            afternoon.
                        </div>
                    </div>
                </div>
                <div class="flex gap-4 max-w-4xl">
                    <div class="w-10 h-10 rounded-xl shrink-0 bg-center bg-cover border border-white/5"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCsrGQ5Bp1E451ifAZamI7jwvoUsQCITm2GesIBa5JF0iP1KQXYWtLn7AAxoQfJuEGZISNeFk6V5BzR2JPfqrsO-8ppV0q25A2ORpidh5YXuAFX8JhcXdIfoicRBgvo403yL2e0VhQXHrRabKUFRUhcKUOOSe7LDH94FtVP8dsNviIRaRYr2RH3lVFOfhPWV7v4zyJo8wuwePqI_lH2Z0LyRQRnXykS9pe2VHfPGamJO4r6ZNHN7XM3d3W3LrLGbV3mc2NGKWWXp24')">
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white">Alex Rivera</span>
                            <span class="text-[10px] text-[#5a6b7a]">11:30 AM</span>
                        </div>
                        <div
                            class="bg-[#1c2127] border border-white/5 px-4 py-3 rounded-2xl rounded-tl-none text-sm leading-relaxed text-[#d1d5db]">
                            Awesome. Also, here is the color config for the new theme variables:
                        </div>
                        <!-- Code Typing Style -->
                        <!-- <div
                            class="bg-black/40 border border-white/5 rounded-xl p-5 font-mono text-[13px] text-[#137fec]/90 overflow-x-auto leading-relaxed shadow-inner">
                        </div> -->
                    </div>
                </div>
            </div>
            <!-- Messge Box Send Message -->
            <div class="px-8 pb-8 bg-gradient-to-t from-[#0b0f14] via-[#0b0f14] to-transparent pt-4">
                <div class="text-[11px] text-[#5a6b7a] mb-2.5 pl-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#137fec] animate-pulse"></span>
                    <span class="font-bold text-[#137fec]">Alex Rivera</span> is typing...
                </div>
                <div
                    class="glass-panel rounded-2xl flex items-center p-2.5 focus-within:ring-2 focus-within:ring-[#137fec]/40 transition-all shadow-xl">


                    <input
                        class="flex-1 bg-transparent border-none text-sm text-white placeholder:text-[#5a6b7a] focus:ring-0 px-2 outline-none"
                        placeholder="Write a message to Alex..." type="text" />
                    <div class="flex items-center gap-1.5 pr-1">
                        <button
                            class="p-2.5 px-3 bg-[#137fec] text-white rounded-xl hover:bg-[#137fec]/90 transition-all ml-2 shadow-lg shadow-[#137fec]/30 active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined">send</span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- <h1>Team Chat</h1>-->
</template>

<style type="text/tailwindcss">
::-webkit-scrollbar {
    width: 5px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #2d333b;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #3b4754;
}
</style>
