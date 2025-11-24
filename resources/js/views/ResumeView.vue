<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const skills = ref([]);
const experiences = ref([]);
const education = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const [skillsRes, expRes, eduRes] = await Promise.all([
      axios.get('/api/skills'),
      axios.get('/api/experiences'),
      axios.get('/api/education')
    ]);
    skills.value = skillsRes.data;
    experiences.value = expRes.data;
    education.value = eduRes.data;
  } catch (error) {
    console.error('Error fetching resume data:', error);
  } finally {
    loading.value = false;
  }
});

const skillsByCategory = computed(() => {
  return skills.value.reduce((acc, skill) => {
    if (!acc[skill.category]) acc[skill.category] = [];
    acc[skill.category].push(skill);
    return acc;
  }, {});
});
</script>

<template>
  <div class="max-w-4xl mx-auto px-4">
    <h1 class="text-4xl font-bold mb-12 text-center bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">Resume</h1>

    <div v-if="loading" class="flex justify-center">
       <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
    </div>

    <div v-else class="space-y-16">
      <!-- Skills Section -->
      <section>
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
          <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
          Skills
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div v-for="(categorySkills, category) in skillsByCategory" :key="category" class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-indigo-300 mb-4">{{ category }}</h3>
            <div class="space-y-4">
              <div v-for="skill in categorySkills" :key="skill.id">
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-300">{{ skill.name }}</span>
                  <span class="text-gray-500">{{ skill.proficiency }}%</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                  <div class="bg-indigo-500 h-2 rounded-full transition-all duration-1000" :style="{ width: skill.proficiency + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Experience Section -->
      <section v-if="experiences.length > 0">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
          <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
          Experience
        </h2>
        <div class="space-y-8 border-l-2 border-gray-700 ml-3 pl-8 relative">
          <div v-for="exp in experiences" :key="exp.id" class="relative">
            <span class="absolute -left-[41px] top-1 h-5 w-5 rounded-full border-4 border-gray-900 bg-indigo-500"></span>
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-indigo-500 transition-colors">
              <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-2">
                <h3 class="text-xl font-bold text-white">{{ exp.role }}</h3>
                <span class="text-indigo-300 text-sm font-medium bg-indigo-500/10 px-3 py-1 rounded-full">{{ exp.start_date }} - {{ exp.end_date || 'Present' }}</span>
              </div>
              <h4 class="text-lg text-gray-400 mb-4">{{ exp.company }}</h4>
              <p class="text-gray-300 leading-relaxed whitespace-pre-line">{{ exp.description }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Education Section -->
      <section v-if="education.length > 0">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
          <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
          Education
        </h2>
        <div class="space-y-8 border-l-2 border-gray-700 ml-3 pl-8 relative">
          <div v-for="edu in education" :key="edu.id" class="relative">
            <span class="absolute -left-[41px] top-1 h-5 w-5 rounded-full border-4 border-gray-900 bg-cyan-500"></span>
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-cyan-500 transition-colors">
              <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-2">
                <h3 class="text-xl font-bold text-white">{{ edu.degree }}</h3>
                <span class="text-cyan-300 text-sm font-medium bg-cyan-500/10 px-3 py-1 rounded-full">{{ edu.start_date }} - {{ edu.end_date || 'Present' }}</span>
              </div>
              <h4 class="text-lg text-gray-400 mb-2">{{ edu.institution }}</h4>
              <p v-if="edu.description" class="text-gray-300 leading-relaxed">{{ edu.description }}</p>
            </div>
          </div>
        </div>
      </section>
      
      <div v-else class="text-center text-gray-500 py-12">
        <p>Experience details not available yet.</p>
      </div>
    </div>
  </div>
</template>
