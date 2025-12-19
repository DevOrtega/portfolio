<script setup>
</script>

<template>
    <div class="h-auto min-h-[3rem] bg-gray-900 border-t border-gray-700 flex flex-wrap items-center px-4 py-2 gap-x-6 gap-y-2 overflow-x-auto text-[10px] sm:text-xs text-gray-300 z-[1000] shadow-md">
        <span class="font-bold text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-900 pr-2">{{ $t('hiking.legend.title') }}</span>
        
        <!-- Route Points -->
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#10b981] border border-white flex items-center justify-center text-[8px] font-bold text-white">S</div> {{ $t('hiking.legend.start') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#f59e0b] border border-white flex items-center justify-center text-[8px] font-bold text-white">#</div> {{ $t('hiking.legend.waypoint') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#ef4444] border border-white flex items-center justify-center text-[8px] font-bold text-white">D</div> {{ $t('hiking.legend.end') }}</div>
        
        <div class="hidden sm:block w-px h-4 bg-gray-700"></div>

        <!-- POIs -->
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#ef4444] border border-white flex items-center justify-center text-[8px] font-bold text-white">R</div> {{ $t('hiking.legend.food') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#06b6d4] border border-white flex items-center justify-center text-[8px] font-bold text-white">W</div> {{ $t('hiking.legend.water') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#8b5cf6] border border-white flex items-center justify-center text-[8px] font-bold text-white">V</div> {{ $t('hiking.legend.viewpoint') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#fb7185] border border-white flex items-center justify-center text-[8px] font-bold text-white">X</div> {{ $t('hiking.legend.picnic') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#16a34a] border border-white flex items-center justify-center text-[8px] font-bold text-white">C</div> {{ $t('hiking.legend.camping') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#78350f] border border-white flex items-center justify-center text-[8px] font-bold text-white">M</div> {{ $t('hiking.legend.culture') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#db2777] border border-white flex items-center justify-center text-[8px] font-bold text-white">H</div> {{ $t('hiking.legend.health') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#d97706] border border-white flex items-center justify-center text-[8px] font-bold text-white">S</div> {{ $t('hiking.legend.shelter') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#4f46e5] border border-white flex items-center justify-center text-[8px] font-bold text-white">A</div> {{ $t('hiking.legend.accommodation') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#374151] border border-white flex items-center justify-center text-[8px] font-bold text-white">P</div> {{ $t('hiking.legend.parking') }}</div>
        <div class="flex items-center gap-1.5"><div class="w-4 h-4 rounded-full bg-[#10b981] border border-white flex items-center justify-center text-[8px] font-bold text-white">^</div> {{ $t('hiking.legend.peak') }}</div>
    </div>
</template>
