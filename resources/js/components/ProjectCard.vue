<template>
  <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 hover:border-indigo-500 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group flex flex-col">
    <!-- Image -->
    <div class="h-48 bg-gray-700 flex items-center justify-center overflow-hidden relative shrink-0">
      <span 
        v-if="!project.image_path" 
        class="text-gray-500 text-4xl font-bold opacity-20 group-hover:scale-110 transition-transform duration-500"
      >
        {{ project.title.charAt(0) }}
      </span>
      <img 
        v-else 
        :src="project.image_path" 
        :alt="project.title" 
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
      >
      
      <!-- Overlay with action buttons - visible on mobile, hover on desktop -->
      <div class="absolute inset-0 bg-black/50 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4">
        <a 
          v-if="project.github_url" 
          :href="project.github_url" 
          target="_blank" 
          class="p-3 md:p-2 bg-white text-gray-900 rounded-full hover:bg-gray-200 active:bg-gray-300 transition touch-manipulation" 
          title="View Code"
          @click.stop
        >
          <svg class="size-6" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path>
          </svg>
        </a>
        
        <!-- Check if URL is internal (starts with /) or external -->
        <component 
          :is="project.url?.startsWith('/') ? 'router-link' : 'a'" 
          v-if="project.url" 
          :to="project.url?.startsWith('/') ? project.url : undefined"
          :href="!project.url?.startsWith('/') ? project.url : undefined"
          :target="!project.url?.startsWith('/') ? '_blank' : undefined"
          class="p-3 md:p-2 bg-indigo-500 text-white rounded-full hover:bg-indigo-400 active:bg-indigo-600 transition touch-manipulation" 
          title="View Live"
          @click.stop
        >
          <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
          </svg>
        </component>
      </div>
    </div>
    
    <!-- Content -->
    <div class="p-6 flex flex-col flex-grow">
      <h3 class="text-xl font-bold text-white mb-2">{{ project.title }}</h3>
      <p class="text-gray-400 text-sm mb-4 line-clamp-3 flex-grow">{{ project.description }}</p>
      
      <!-- Tags -->
      <div class="flex flex-wrap gap-2 mt-auto">
        <span 
          v-for="tag in project.tags" 
          :key="tag" 
          class="px-2 py-1 text-xs font-medium bg-gray-700 text-indigo-300 rounded-md border border-gray-600"
        >
          {{ tag }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  project: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.title && value.description && Array.isArray(value.tags);
    }
  }
});
</script>
