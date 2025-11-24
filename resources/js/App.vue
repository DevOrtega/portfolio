<template>
  <div class="min-h-screen bg-gray-900 text-gray-100 font-sans selection:bg-indigo-500 selection:text-white">
    <NavBar />
    <main class="container mx-auto px-4 py-8">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
    <footer class="text-center py-6 text-gray-500 text-sm">
      &copy; {{ new Date().getFullYear() }} Carlos Ortega. Built with Laravel & Vue.
    </footer>
  </div>
</template>

<script setup>
import NavBar from './components/NavBar.vue';
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
