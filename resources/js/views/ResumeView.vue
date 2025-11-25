<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import SectionHeader from '../components/SectionHeader.vue';
import TimelineItem from '../components/TimelineItem.vue';

const skills = ref([]);
const experiences = ref([]);
const education = ref([]);
const loading = ref(true);

// Store all data (unfiltered) for calculating available years
const allExperiences = ref([]);
const allEducation = ref([]);

// Year filter for skills and experiences
const selectedYear = ref('all');

// Fetch data function
const fetchData = async () => {
  loading.value = true;
  try {
    const yearParam = selectedYear.value !== 'all' ? `?year=${selectedYear.value}` : '';
    
    const [skillsRes, expRes, eduRes] = await Promise.all([
      axios.get(`/api/skills${yearParam}`),
      axios.get(`/api/experiences${yearParam}`),
      axios.get(`/api/education${yearParam}`)
    ]);
    skills.value = skillsRes.data;
    experiences.value = expRes.data;
    education.value = eduRes.data;
    
    // Store unfiltered data on first load
    if (selectedYear.value === 'all') {
      allExperiences.value = expRes.data;
      allEducation.value = eduRes.data;
    }
  } catch (error) {
    console.error('Error fetching resume data:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await fetchData();
});

// Watch for year changes and refetch data
watch(selectedYear, async () => {
  await fetchData();
});

// Experiences are already sorted by backend (most recent first)
const sortedExperiences = computed(() => {
  return experiences.value;
});

// Education is already sorted by backend (most recent first)
const sortedEducation = computed(() => {
  return education.value;
});

// Extract unique years from experiences and education for filter
// Use unfiltered data so years don't disappear when filtering
const availableYears = computed(() => {
  const years = new Set();
  
  // Extract years from all experiences (unfiltered)
  allExperiences.value.forEach(exp => {
    const startYear = parseInt(exp.start_date.split(' ').pop());
    const endYear = exp.end_date ? parseInt(exp.end_date.split(' ').pop()) : new Date().getFullYear();
    
    if (!isNaN(startYear)) {
      for (let year = startYear; year <= endYear; year++) {
        years.add(year);
      }
    }
  });
  
  // Extract years from all education (unfiltered)
  allEducation.value.forEach(edu => {
    const startYear = parseInt(edu.start_date.split(' ').pop());
    const endYear = edu.end_date ? parseInt(edu.end_date.split(' ').pop()) : new Date().getFullYear();
    
    if (!isNaN(startYear)) {
      for (let year = startYear; year <= endYear; year++) {
        years.add(year);
      }
    }
  });
  
  // Always include current year
  years.add(new Date().getFullYear());
  
  return Array.from(years).sort((a, b) => b - a);
});

// Skills are already filtered and sorted by backend (proficiency DESC)
// Just group them by category
const skillsByCategory = computed(() => {
  return skills.value.reduce((acc, skill) => {
    if (!acc[skill.category]) acc[skill.category] = [];
    acc[skill.category].push(skill);
    return acc;
  }, {});
});

// Translate category name to i18n key
const getCategoryTranslation = (category) => {
  const categoryMap = {
    'Backend': 'skills.backend',
    'Frontend': 'skills.frontend',
    'Database': 'skills.database',
    'DevOps': 'skills.devops',
    'Tools': 'skills.tools',
    'Methodology': 'skills.methodology'
  };
  return categoryMap[category] || category;
};
</script>

<template>
  <div class="max-w-4xl mx-auto px-4">
    <h1 class="text-4xl font-bold mb-12 text-center bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">{{ $t('resume.title') }}</h1>

    <LoadingSpinner v-if="loading" />

    <div v-else class="space-y-16">
      <!-- Skills Section -->
      <section>
        <SectionHeader :title="$t('skills.title')" default-icon="skills" />
        
        <!-- Year Filter -->
        <div class="mb-6 flex items-center gap-4">
          <label for="year-filter" class="text-gray-300 font-medium">{{ $t('skills.filterByYear') }}:</label>
          <select 
            id="year-filter"
            v-model="selectedYear" 
            class="bg-gray-800 text-white px-4 py-2 rounded-lg border border-gray-700 focus:border-indigo-500 focus:outline-none transition-colors"
          >
            <option value="all">{{ $t('skills.allYears') }}</option>
            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
          </select>
          <span v-if="selectedYear !== 'all'" class="text-sm text-gray-400">
            ({{ $t('skills.showingSkillsFor') }} {{ selectedYear }})
          </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div v-for="(categorySkills, category) in skillsByCategory" :key="category" class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-indigo-300 mb-4">{{ $t(getCategoryTranslation(category)) }}</h3>
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
      <section v-if="sortedExperiences.length > 0">
        <SectionHeader :title="$t('experience.title')" default-icon="experience" />
        <div class="space-y-8 border-l-2 border-gray-700 ml-3 pl-8 relative">
          <TimelineItem
            v-for="exp in sortedExperiences"
            :key="exp.id"
            :title="exp.role"
            :subtitle="exp.company"
            :date-range="`${exp.start_date} - ${exp.end_date || $t('resume.present')}`"
            :description="exp.description"
            color="indigo"
          />
        </div>
      </section>

      <!-- Education Section -->
      <section v-if="sortedEducation.length > 0">
        <SectionHeader :title="$t('education.title')" default-icon="education" />
        <div class="space-y-8 border-l-2 border-gray-700 ml-3 pl-8 relative">
          <TimelineItem
            v-for="edu in sortedEducation"
            :key="edu.id"
            :title="edu.degree"
            :subtitle="edu.institution"
            :date-range="`${edu.start_date} - ${edu.end_date || $t('resume.present')}`"
            :description="edu.description || ''"
            color="cyan"
          />
        </div>
      </section>
      
      <div v-else class="text-center text-gray-500 py-12">
        <p>{{ $t('resume.notAvailable') }}</p>
      </div>
    </div>
  </div>
</template>
