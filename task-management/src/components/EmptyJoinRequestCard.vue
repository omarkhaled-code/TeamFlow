<template>

    <div class="bg-[#1a232e] border border-[#283039] rounded-xl p-4">
        <div class=" flex flex-col justify-center items-center gap-2">
            <div class="flex gap-3  items-center w-full">
                <div class="h-8 w-8 rounded-lg flex items-center justify-between text-blue-500">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-1">{{ $t('emptyState.noPendingRequests') }}</h3>
            </div>

            <p class="text-sm text-[#9dabb9] line-clamp-2">
                {{ $t("emptyState.noPendingRequestsDesc") }}
            </p>
        </div>

    </div>

</template>
