<template>
  <nav class="bg-gray-800/50 backdrop-blur-md border-b border-gray-700 sticky top-0 z-50">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <router-link to="/" class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">
          DevOrtega
        </router-link>
        
        <div class="hidden md:flex space-x-8">
          <router-link 
            v-for="item in navItems" 
            :key="item.path" 
            :to="item.path"
            class="text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-all duration-200 text-sm font-medium"
            active-class="text-indigo-400 bg-gray-800"
          >
            {{ item.name }}
          </router-link>
          <a href="/api/documentation" target="_blank" class="text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-all duration-200 text-sm font-medium">
            API Docs
          </a>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
const navItems = [
  { name: 'Home', path: '/' },
  { name: 'Projects', path: '/projects' },
  { name: 'Resume', path: '/resume' },
];
</script>
