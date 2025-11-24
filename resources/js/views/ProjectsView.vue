<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import ProjectCard from '../components/ProjectCard.vue';

const projects = ref([]);
const loading = ref(true);

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
  <div class="max-w-6xl mx-auto px-4">
    <h1 class="text-4xl font-bold mb-12 text-center bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">Projects</h1>
    
    <LoadingSpinner v-if="loading" />
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <ProjectCard v-for="project in projects" :key="project.id" :project="project" />
    </div>
  </div>
</template>
