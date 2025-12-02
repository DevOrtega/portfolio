<template>
  <nav class="bg-gray-800/50 backdrop-blur-md border-b border-gray-700 sticky top-0 z-50">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <router-link to="/" class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">
          DevOrtega
        </router-link>
        
        <!-- Desktop Menu -->
        <div class="hidden md:flex space-x-8 items-center">
          <router-link 
            v-for="item in navItems" 
            :key="item.path" 
            :to="item.path"
            class="text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-all duration-200 text-sm font-medium"
            active-class="text-indigo-400 bg-gray-800"
          >
            {{ $t(item.name) }}
          </router-link>
          <a href="/api/documentation" target="_blank" class="text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-all duration-200 text-sm font-medium">
            {{ $t('nav.apiDocs') }}
          </a>
          <LanguageSwitcher />
        </div>

        <!-- Mobile Menu Button -->
        <button 
          @click="isMenuOpen = !isMenuOpen" 
          class="md:hidden p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700/50 transition-colors"
          aria-label="Toggle menu"
        >
          <svg v-if="!isMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div 
        v-show="isMenuOpen" 
        class="md:hidden border-t border-gray-700 py-4 space-y-2"
      >
        <router-link 
          v-for="item in navItems" 
          :key="item.path" 
          :to="item.path"
          @click="isMenuOpen = false"
          class="block text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-all duration-200 text-sm font-medium"
          active-class="text-indigo-400 bg-gray-800"
        >
          {{ $t(item.name) }}
        </router-link>
        <a 
          href="/api/documentation" 
          target="_blank" 
          class="block text-gray-300 hover:text-white hover:bg-gray-700/50 px-3 py-2 rounded-md transition-all duration-200 text-sm font-medium"
        >
          {{ $t('nav.apiDocs') }}
        </a>
        <div class="px-3 pt-2">
          <LanguageSwitcher />
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import LanguageSwitcher from './LanguageSwitcher.vue';

const isMenuOpen = ref(false);

const navItems = [
  { name: 'nav.home', path: '/' },
  { name: 'nav.projects', path: '/projects' },
  { name: 'nav.experience', path: '/resume' },
];
</script>
