<template>
  <div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
      <router-link to="/projects" class="text-indigo-400 hover:text-indigo-300 flex items-center gap-2 mb-4 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Projects
      </router-link>
      <h1 class="text-3xl font-bold text-white mb-2">Vue Leaflet Integration</h1>
      <p class="text-gray-400">A demonstration of interactive maps using Vue 3 and Leaflet.</p>
    </div>
    
    <MapDemo />
    
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4">Features Implemented</h3>
        <ul class="space-y-2 text-gray-300 list-disc list-inside">
          <li>Interactive OpenStreetMap tiles</li>
          <li>Custom markers with popups</li>
          <li>Geometric shapes (Circles, Polygons)</li>
          <li>Responsive container</li>
          <li>Vue 3 Composition API integration</li>
        </ul>
      </div>
      <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4">Tech Stack</h3>
        <div class="flex flex-wrap gap-2">
          <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-sm">Vue.js 3</span>
          <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm">Leaflet</span>
          <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-sm">OpenStreetMap</span>
          <span class="px-3 py-1 bg-cyan-500/20 text-cyan-300 rounded-full text-sm">TailwindCSS</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import MapDemo from '../components/MapDemo.vue';
</script>
