<script setup>
import { computed } from 'vue'

const props = defineProps({
    teamId: Number,
    teamTitle: String,
    teamDescription: String,
    doneTasks: Number,
    totalTasks: Number,
    membersCount: Number,
    colorTheme: {
        type: String,
        default: 'indigo'
    }
})



const theme = computed(() => {
    const themes = {
        indigo: {
            iconBg: 'bg-indigo-500/20',
            iconText: 'text-indigo-400',
            badgeBg: 'bg-indigo-500/10',
            badgeText: 'text-indigo-400',
            progress: 'bg-indigo-500',
            hoverBorder: 'hover:border-indigo-500/50',
            hoverButton: 'hover:bg-indigo-500'
        },

        green: {
            iconBg: 'bg-emerald-500/20',
            iconText: 'text-emerald-400',
            badgeBg: 'bg-emerald-500/10',
            badgeText: 'text-emerald-400',
            progress: 'bg-emerald-500',
            hoverBorder: 'hover:border-emerald-500/50',
            hoverButton: 'hover:bg-emerald-500'
        },
        yellow: {
            iconBg: 'bg-amber-500/20',
            iconText: 'text-amber-400',
            badgeBg: 'bg-amber-500/10',
            badgeText: 'text-amber-400',
            progress: 'bg-amber-500',
            hoverBorder: 'hover:border-amber-500/50',
            hoverButton: 'hover:bg-amber-500'
        },
        red: {
            iconBg: 'bg-red-500/20',
            iconText: 'text-red-400',
            badgeBg: 'bg-red-500/10',
            badgeText: 'text-red-400',
            progress: 'bg-red-500',
            hoverBorder: 'hover:border-red-500/50',
            hoverButton: 'hover:bg-red-500'
        },
        purple: {
            iconBg: 'bg-purple-500/20',
            iconText: 'text-purple-400',
            badgeBg: 'bg-purple-500/10',
            badgeText: 'text-purple-400',
            progress: 'bg-purple-500',
            hoverBorder: 'hover:border-purple-500/50',
            hoverButton: 'hover:bg-purple-500'
        }
    }

    return themes[props.colorTheme] || themes.indigo
})


const getPerecentValue = computed(() => {
    if (props.totalTasks === 0) return 0;
    return Math.round((props.doneTasks / props.totalTasks) * 100);
})


</script>




<template>
    <div class="bg-[#1a232e] border border-[#283039] transition-all rounded-xl p-5 group" :class="theme.hoverBorder">
        <div class="flex justify-between items-center mb-4 ">
            <div class="h-12 w-12 rounded-lg flex items-center justify-center" :class="[theme.iconBg, theme.iconText]">
                <!-- <span class="material-symbols-outlined" style="font-size: 28px;">design_services</span> -->
                <span class="material-symbols-outlined">
                    stacks
                </span>
            </div>
            <button class="text-[#9dabb9] hover:text-white">
                <span class="text-xs font-semibold px-2 py-1 rounded" :class="[theme.badgeBg, theme.badgeText]">{{
                    props.membersCount }}
                    {{ $t('teamCard.members') }}</span>
            </button>
        </div>
        <h4 class="text-lg font-bold mb-1">{{ props.teamTitle }}</h4>
        <p class="text-sm text-[#9dabb9] mb-4 line-clamp-2">{{ props.teamDescription }}</p>

        <div class="space-y-2 mb-4">
            <div class="flex justify-between text-xs font-medium">
                <span class="text-[#9dabb9]">{{ $t('teamCard.activeTasks') }}</span>
                <span class="text-white">{{ props.doneTasks }}/{{ props.totalTasks }}</span>
            </div>
            <div class="w-full bg-[#283039] h-1.5 rounded-full overflow-hidden">
                <div class="h-full rounded-full" :class="theme.progress" :style="{ width: `${getPerecentValue}%` }">
                </div>
            </div>
        </div>
        <!-- <button> -->
        <router-link :to="{ name: 'team', params: { id: props.teamId } }"
            class=" inline-block text-center w-full py-2 bg-[#283039] text-white text-sm font-bold rounded-lg cursor-pointer transition-colors duration-200"
            :class="theme.hoverButton">{{ $t('teamCard.openTeamWorkspace') }}</router-link>
        <!-- </button> -->
    </div>
</template>