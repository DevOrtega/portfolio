<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import LoadingSpinner from '@/components/LoadingSpinner.vue';
import { useLocale } from '@/composables/useLocale';

const { t, locale } = useI18n();
const { onLocaleChange } = useLocale();

const personalInfo = ref(null);
const loading = ref(true);

// Form state
const form = ref({
  name: '',
  email: '',
  subject: '',
  message: '',
  website: '', // Honeypot field
});

const errors = ref({});
const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref('');

// Character counters
const messageMaxLength = 5000;
const subjectMaxLength = 200;

const messageLength = computed(() => form.value.message.length);
const subjectLength = computed(() => form.value.subject.length);

/**
 * Fetch personal info from API
 */
const fetchData = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/personal-info');
    personalInfo.value = response.data;
  } catch (error) {
    console.error('Error fetching personal info:', error);
  } finally {
    loading.value = false;
  }
};

// Subscribe to locale changes
let unsubscribe;
onMounted(async () => {
  await fetchData();
  unsubscribe = onLocaleChange(() => fetchData());
});

onUnmounted(() => {
  if (unsubscribe) unsubscribe();
});

const validateForm = () => {
  errors.value = {};
  
  // Name validation
  if (!form.value.name.trim()) {
    errors.value.name = t('contact.errors.nameRequired');
  } else if (form.value.name.length < 2) {
    errors.value.name = t('contact.errors.nameMin');
  } else if (!/^[\p{L}\s\-\.]+$/u.test(form.value.name)) {
    errors.value.name = t('contact.errors.nameInvalid');
  }
  
  // Email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!form.value.email.trim()) {
    errors.value.email = t('contact.errors.emailRequired');
  } else if (!emailRegex.test(form.value.email)) {
    errors.value.email = t('contact.errors.emailInvalid');
  }
  
  // Subject validation
  if (!form.value.subject.trim()) {
    errors.value.subject = t('contact.errors.subjectRequired');
  } else if (form.value.subject.length < 5) {
    errors.value.subject = t('contact.errors.subjectMin');
  }
  
  // Message validation
  if (!form.value.message.trim()) {
    errors.value.message = t('contact.errors.messageRequired');
  } else if (form.value.message.length < 10) {
    errors.value.message = t('contact.errors.messageMin');
  }
  
  return Object.keys(errors.value).length === 0;
};

const submitForm = async () => {
  if (!validateForm()) return;
  
  isSubmitting.value = true;
  submitError.value = '';
  
  try {
    const response = await axios.post('/api/contact', {
      ...form.value,
      locale: locale.value
    });
    
    if (response.data.success) {
      submitSuccess.value = true;
      // Reset form
      form.value = {
        name: '',
        email: '',
        subject: '',
        message: '',
        website: '',
      };
    } else {
      submitError.value = response.data.message || t('contact.errors.generic');
    }
  } catch (error) {
    if (error.response?.status === 422) {
      // Validation errors from server
      const serverErrors = error.response.data.errors;
      for (const field in serverErrors) {
        errors.value[field] = serverErrors[field][0];
      }
    } else if (error.response?.status === 429) {
      submitError.value = t('contact.errors.tooMany');
    } else {
      submitError.value = t('contact.errors.generic');
    }
  } finally {
    isSubmitting.value = false;
  }
};

const resetSuccess = () => {
  submitSuccess.value = false;
};
</script>

<template>
  <div class="min-h-[85vh] py-12 px-4 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
      <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-600/20 rounded-full blur-[120px] animate-pulse-slow"></div>
      <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-cyan-600/20 rounded-full blur-[120px] animate-pulse-slow delay-1000"></div>
    </div>

    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-12 animate-fade-in">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $t('contact.title') }}</h1>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">{{ $t('contact.subtitle') }}</p>
      </div>

      <div v-if="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-12 animate-fade-in">
        
        <!-- Contact Info & Social Links -->
        <div class="space-y-8">
          <!-- Social Links -->
          <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl p-8">
            <h2 class="text-2xl font-semibold text-white mb-6">{{ $t('contact.connectWith') }}</h2>
            
            <div class="space-y-4">
              <!-- Email -->
              <a v-if="personalInfo?.email" 
                 :href="'mailto:' + personalInfo.email" 
                 class="flex items-center gap-4 p-4 bg-gray-700/50 rounded-xl hover:bg-gray-700 transition-all duration-300 group">
                <div class="size-12 bg-gradient-to-br from-red-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="size-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-gray-400 text-sm">{{ $t('contact.email') }}</p>
                  <p class="text-white group-hover:text-indigo-400 transition-colors">{{ personalInfo.email }}</p>
                </div>
              </a>
              
              <!-- LinkedIn -->
              <a v-if="personalInfo?.linkedin_url" 
                 :href="personalInfo.linkedin_url" 
                 target="_blank"
                 class="flex items-center gap-4 p-4 bg-gray-700/50 rounded-xl hover:bg-gray-700 transition-all duration-300 group">
                <div class="size-12 bg-gradient-to-br from-blue-600 to-blue-400 rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="size-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-gray-400 text-sm">LinkedIn</p>
                  <p class="text-white group-hover:text-blue-400 transition-colors">{{ $t('contact.viewProfile') }}</p>
                </div>
                <svg class="size-5 text-gray-500 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
              </a>
              
              <!-- GitHub -->
              <a v-if="personalInfo?.github_url" 
                 :href="personalInfo.github_url" 
                 target="_blank"
                 class="flex items-center gap-4 p-4 bg-gray-700/50 rounded-xl hover:bg-gray-700 transition-all duration-300 group">
                <div class="size-12 bg-gradient-to-br from-gray-600 to-gray-800 rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="size-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <p class="text-gray-400 text-sm">GitHub</p>
                  <p class="text-white group-hover:text-gray-300 transition-colors">{{ $t('contact.viewCode') }}</p>
                </div>
                <svg class="size-5 text-gray-500 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
              </a>
            </div>
          </div>
          
          <!-- Additional Info -->
          <div class="bg-gradient-to-br from-indigo-500/10 to-cyan-500/10 border border-indigo-500/20 rounded-2xl p-8">
            <h3 class="text-xl font-semibold text-white mb-4">{{ $t('contact.availability') }}</h3>
            <p class="text-gray-300 leading-relaxed">{{ $t('contact.availabilityText') }}</p>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl p-8">
          <h2 class="text-2xl font-semibold text-white mb-6">{{ $t('contact.sendMessage') }}</h2>
          
          <!-- Success Message -->
          <div v-if="submitSuccess" class="mb-6 p-6 bg-green-500/10 border border-green-500/30 rounded-xl">
            <div class="flex items-center gap-3 mb-3">
              <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <h3 class="text-lg font-semibold text-green-400">{{ $t('contact.successTitle') }}</h3>
            </div>
            <p class="text-green-300 mb-4">{{ $t('contact.successMessage') }}</p>
            <button @click="resetSuccess" class="text-green-400 hover:text-green-300 underline text-sm">
              {{ $t('contact.sendAnother') }}
            </button>
          </div>
          
          <!-- Error Message -->
          <div v-if="submitError" class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl">
            <div class="flex items-center gap-2">
              <svg class="size-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="text-red-400">{{ submitError }}</p>
            </div>
          </div>
          
          <!-- Form -->
          <form v-if="!submitSuccess" @submit.prevent="submitForm" class="space-y-5">
            <!-- Honeypot (hidden) -->
            <input type="text" v-model="form.website" class="hidden" tabindex="-1" autocomplete="off" />
            
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-300 mb-2">{{ $t('contact.form.name') }} *</label>
              <input
                type="text"
                id="name"
                v-model="form.name"
                :class="['w-full px-4 py-3 bg-gray-700/50 border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all', errors.name ? 'border-red-500' : 'border-gray-600']"
                :placeholder="$t('contact.form.namePlaceholder')"
                maxlength="100"
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-400">{{ errors.name }}</p>
            </div>
            
            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-300 mb-2">{{ $t('contact.form.email') }} *</label>
              <input
                type="email"
                id="email"
                v-model="form.email"
                :class="['w-full px-4 py-3 bg-gray-700/50 border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all', errors.email ? 'border-red-500' : 'border-gray-600']"
                :placeholder="$t('contact.form.emailPlaceholder')"
                maxlength="255"
              />
              <p v-if="errors.email" class="mt-1 text-sm text-red-400">{{ errors.email }}</p>
            </div>
            
            <!-- Subject -->
            <div>
              <label for="subject" class="block text-sm font-medium text-gray-300 mb-2">{{ $t('contact.form.subject') }} *</label>
              <input
                type="text"
                id="subject"
                v-model="form.subject"
                :class="['w-full px-4 py-3 bg-gray-700/50 border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all', errors.subject ? 'border-red-500' : 'border-gray-600']"
                :placeholder="$t('contact.form.subjectPlaceholder')"
                :maxlength="subjectMaxLength"
              />
              <div class="flex justify-between mt-1">
                <p v-if="errors.subject" class="text-sm text-red-400">{{ errors.subject }}</p>
                <span class="text-xs text-gray-500 ml-auto">{{ subjectLength }}/{{ subjectMaxLength }}</span>
              </div>
            </div>
            
            <!-- Message -->
            <div>
              <label for="message" class="block text-sm font-medium text-gray-300 mb-2">{{ $t('contact.form.message') }} *</label>
              <textarea
                id="message"
                v-model="form.message"
                rows="5"
                :class="['w-full px-4 py-3 bg-gray-700/50 border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all resize-none', errors.message ? 'border-red-500' : 'border-gray-600']"
                :placeholder="$t('contact.form.messagePlaceholder')"
                :maxlength="messageMaxLength"
              ></textarea>
              <div class="flex justify-between mt-1">
                <p v-if="errors.message" class="text-sm text-red-400">{{ errors.message }}</p>
                <span class="text-xs text-gray-500 ml-auto">{{ messageLength }}/{{ messageMaxLength }}</span>
              </div>
            </div>
            
            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-indigo-500/25 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="isSubmitting" class="animate-spin size-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isSubmitting ? $t('contact.form.sending') : $t('contact.form.send') }}</span>
              <svg v-if="!isSubmitting" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
            </button>
          </form>
        </div>
      </div>

      <LoadingSpinner v-else size="large" />
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

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
