<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import SectionHeader from '../components/SectionHeader.vue';
import TimelineItem from '../components/TimelineItem.vue';

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

    <LoadingSpinner v-if="loading" />

    <div v-else class="space-y-16">
      <!-- Skills Section -->
      <section>
        <SectionHeader title="Skills" default-icon="skills" />
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
        <SectionHeader title="Experience" default-icon="experience" />
        <div class="space-y-8 border-l-2 border-gray-700 ml-3 pl-8 relative">
          <TimelineItem
            v-for="exp in experiences"
            :key="exp.id"
            :title="exp.role"
            :subtitle="exp.company"
            :date-range="`${exp.start_date} - ${exp.end_date || 'Present'}`"
            :description="exp.description"
            color="indigo"
          />
        </div>
      </section>

      <!-- Education Section -->
      <section v-if="education.length > 0">
        <SectionHeader title="Education" default-icon="education" />
        <div class="space-y-8 border-l-2 border-gray-700 ml-3 pl-8 relative">
          <TimelineItem
            v-for="edu in education"
            :key="edu.id"
            :title="edu.degree"
            :subtitle="edu.institution"
            :date-range="`${edu.start_date} - ${edu.end_date || 'Present'}`"
            :description="edu.description || ''"
            color="cyan"
          />
        </div>
      </section>
      
      <div v-else class="text-center text-gray-500 py-12">
        <p>Experience details not available yet.</p>
      </div>
    </div>
  </div>
</template>
