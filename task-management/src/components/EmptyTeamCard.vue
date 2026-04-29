<template>
    <div
        class="bg-[#1a232e] border border-[#283039] transition-all rounded-xl p-5 group flex flex-col justify-center items-center gap-1">
        <div class="h-12 w-12 rounded-lg flex items-center justify-center text-blue-500">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>

        <h3 class="text-left text-lg font-bold mb-1">{{ $t('emptyState.noTeams') }}</h3>
        <p class="text-center text-sm text-[#9dabb9] mb-4 line-clamp-2">
            {{ $t('emptyState.noTeamsDesc') }}
        </p>
    </div>
</template>