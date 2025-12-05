<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LoadingSpinner from '@/components/LoadingSpinner.vue';

const personalInfo = ref(null);
const loading = ref(true);

onMounted(async () => {
  try {
    const response = await axios.get('/api/personal-info');
    personalInfo.value = response.data;
  } catch (error) {
    console.error('Error fetching personal info:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="min-h-[85vh] flex flex-col items-center justify-center px-4 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
      <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-600/20 rounded-full blur-[120px] animate-pulse-slow"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-cyan-600/20 rounded-full blur-[120px] animate-pulse-slow delay-1000"></div>
    </div>

    <div v-if="!loading && personalInfo" class="w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center animate-fade-in">
      
      <!-- Text Content -->
      <div class="order-2 lg:order-1 text-center lg:text-left space-y-8">
        <div class="space-y-4">
          <h1 class="text-5xl md:text-7xl font-bold text-white tracking-tight leading-tight">
            {{ $t('home.greeting') }} <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-cyan-400 animate-gradient">{{ personalInfo.name }}</span>
          </h1>
          
          <p class="text-2xl md:text-3xl text-gray-300 font-light">{{ personalInfo.headline }}</p>
        </div>
        
        <div class="text-lg text-gray-400 max-w-2xl mx-auto lg:mx-0 space-y-6">
          <p v-for="(paragraph, index) in personalInfo.bio.split('\n').filter(p => p.trim())" 
             :key="index" 
             class="leading-relaxed">
            {{ paragraph }}
          </p>
        </div>
        
        <div class="flex flex-wrap justify-center lg:justify-start gap-3 sm:gap-4 pt-4">
          <router-link to="/projects" class="px-5 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white rounded-full transition-all duration-300 shadow-lg hover:shadow-indigo-500/25 flex items-center gap-2 hover:-translate-y-0.5 text-sm sm:text-base">
            <span>{{ $t('home.viewProjects') }}</span>
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
          </router-link>
          <router-link to="/contact" class="px-5 sm:px-8 py-2.5 sm:py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-full transition-all duration-300 border border-gray-700 hover:border-indigo-500 flex items-center gap-2 group shadow-lg hover:shadow-indigo-500/20 text-sm sm:text-base">
            <span>{{ $t('nav.contact') }}</span>
            <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </router-link>
          <router-link to="/resume" class="px-5 sm:px-8 py-2.5 sm:py-3 bg-transparent hover:bg-white/5 text-white rounded-full transition-all duration-300 border border-white/20 hover:border-white/50 flex items-center gap-2 text-sm sm:text-base">
            <span>{{ $t('resume.title') }}</span>
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </router-link>
        </div>
      </div>

      <!-- Image/Visual -->
      <div class="order-1 lg:order-2 flex justify-center relative">
        <div class="relative w-64 h-64 md:w-80 md:h-80 lg:w-96 lg:h-96 mt-8">
            <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-cyan-500 rounded-full blur-2xl opacity-50 animate-pulse-slow"></div>
            <div class="profile-frame relative w-full h-full rounded-full border-4 border-gray-800 shadow-2xl z-10 hover:scale-105 transition-transform duration-500 overflow-hidden bg-gradient-to-b from-gray-700 to-gray-900">
              <img :src="'/images/profile.png'" alt="Carlos Ortega" class="w-full h-full object-cover object-top">
            </div>
            
            <!-- Floating Skill Badges -->
            <div class="absolute top-4 -right-2 md:-right-4 bg-gray-800 border border-gray-700 p-2 rounded-lg shadow-xl animate-float delay-0 z-20">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Vue.js_Logo_2.svg" class="w-8 h-8" alt="Vue">
            </div>
            <div class="absolute top-1/2 -right-6 md:-right-8 bg-gray-800 border border-gray-700 p-2 rounded-lg shadow-xl animate-float delay-1000 z-20">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Laravel.svg" class="w-8 h-8" alt="Laravel">
            </div>
        </div>
      </div>
      
    </div>
    
    <LoadingSpinner v-else size="large" />
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.8s ease-out forwards;
}

.animate-pulse-slow {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 8s ease infinite;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
</style>
