<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import ProjectCard from '../components/ProjectCard.vue';

const projects = ref([]);
const loading = ref(true);

const upcomingDemos = [
  {
    title: 'Sistema ERP',
    description: 'Gestión empresarial integral con módulos de inventario, ventas y recursos humanos'
  },
  {
    title: 'Portal de Investigadores',
    description: 'Plataforma colaborativa para gestión de proyectos de investigación universitaria'
  },
  {
    title: 'Chatbot con Google Assistant',
    description: 'Asistente virtual integrado con Google Assistant para automatización de tareas'
  }
];

onMounted(async () => {
  try {
    const response = await axios.get('/api/projects');
    projects.value = response.data;
  } catch (error) {
    console.error('Error fetching projects:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="max-w-6xl mx-auto px-4 pb-16">
    <h1 class="text-4xl font-bold mb-12 text-center bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">Proyectos</h1>
    
    <LoadingSpinner v-if="loading" />
    
    <div v-else class="space-y-16">
      <!-- Proyectos Actuales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <ProjectCard v-for="project in projects" :key="project.id" :project="project" />
      </div>

      <!-- Próximas Demos -->
      <div class="mt-20">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-white mb-3">Próximas Demos</h2>
          <p class="text-gray-400 text-lg">Proyectos en desarrollo que estarán disponibles próximamente</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div 
            v-for="(demo, index) in upcomingDemos" 
            :key="index"
            class="bg-gray-800/30 border-2 border-dashed border-gray-700 rounded-xl p-6 hover:border-indigo-500/50 transition-all duration-300 group"
          >
            <div class="flex items-start gap-4">
              <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-indigo-500/10 flex items-center justify-center group-hover:bg-indigo-500/20 transition-colors">
                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-indigo-400 transition-colors">{{ demo.title }}</h3>
                <p class="text-sm text-gray-400 leading-relaxed">{{ demo.description }}</p>
              </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs text-gray-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>En proceso</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
