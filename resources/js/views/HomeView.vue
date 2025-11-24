<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

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
          <div class="inline-block px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 backdrop-blur-sm">
            <span class="text-indigo-400 font-medium tracking-wide uppercase text-xs">Portfolio 2025</span>
          </div>
          
          <h1 class="text-5xl md:text-7xl font-bold text-white tracking-tight leading-tight">
            Hola, soy <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-cyan-400 animate-gradient">{{ personalInfo.name }}</span>
          </h1>
          
          <p class="text-2xl md:text-3xl text-gray-300 font-light">{{ personalInfo.headline }}</p>
        </div>
        
        <p class="text-lg text-gray-400 leading-relaxed max-w-2xl mx-auto lg:mx-0">
          {{ personalInfo.bio }}
        </p>
        
        <div class="flex flex-wrap justify-center lg:justify-start gap-4 pt-4">
          <a v-if="personalInfo.github_url" :href="personalInfo.github_url" target="_blank" 
             class="px-8 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-full transition-all duration-300 border border-gray-700 hover:border-indigo-500 flex items-center gap-2 group shadow-lg hover:shadow-indigo-500/20">
            <span>GitHub</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path></svg>
          </a>
          <a v-if="personalInfo.linkedin_url" :href="personalInfo.linkedin_url" target="_blank" 
             class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-full transition-all duration-300 shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 flex items-center gap-2 hover:-translate-y-0.5">
            <span>LinkedIn</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
          </a>
          <router-link to="/projects" class="px-8 py-3 bg-transparent hover:bg-white/5 text-white rounded-full transition-all duration-300 border border-white/20 hover:border-white/50 flex items-center gap-2">
            <span>Ver Proyectos</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
          </router-link>
        </div>
      </div>

      <!-- Image/Visual -->
      <div class="order-1 lg:order-2 flex justify-center relative">
        <div class="relative w-64 h-64 md:w-80 md:h-80 lg:w-96 lg:h-96">
            <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-cyan-500 rounded-full blur-2xl opacity-50 animate-pulse-slow"></div>
            <img :src="'/images/profile.png'" alt="Carlos Ortega" class="relative w-full h-full object-cover rounded-full border-4 border-gray-800 shadow-2xl z-10 hover:scale-105 transition-transform duration-500">
            
            <!-- Floating Skill Badges -->
            <div class="absolute -top-4 -right-4 bg-gray-800 border border-gray-700 p-2 rounded-lg shadow-xl animate-float delay-0 z-20">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Vue.js_Logo_2.svg" class="w-8 h-8" alt="Vue">
            </div>
            <div class="absolute top-1/2 -right-8 bg-gray-800 border border-gray-700 p-2 rounded-lg shadow-xl animate-float delay-1000 z-20">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Laravel.svg" class="w-8 h-8" alt="Laravel">
            </div>
            <div class="absolute -bottom-4 -left-4 bg-gray-800 border border-gray-700 p-2 rounded-lg shadow-xl animate-float delay-2000 z-20">
                <span class="text-2xl">🚀</span>
            </div>
        </div>
      </div>
      
    </div>
    
    <div v-else class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
    </div>
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
