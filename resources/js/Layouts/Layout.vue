<template>
  <div class="min-h-screen flex flex-col bg-gray-900 transition-colors duration-500">
    <header class="flex items-center justify-between p-4 bg-gray-800 shadow transition-colors duration-500">
      <h1 class="text-2xl font-bold text-white">
        Библиотека
      </h1>

      <div class="ml-auto" v-if="isAuthenticated">
        <form method="POST" action="/logout" @submit.prevent="submitLogout">
          <button type="submit"
                  class="text-sm font-medium text-gray-300 hover:text-white bg-gray-700 hover:bg-gray-600
                     px-5 py-2 rounded-lg transition-all shadow hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 active:scale-95">
            Выйти
          </button>
        </form>
      </div>
    </header>

    <main class="flex-1 p-6">
      <slot />
    </main>

    <footer class="p-4 text-center text-sm text-gray-400 transition-colors duration-500">
      &copy; {{ new Date().getFullYear() }} Все права защищены
    </footer>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Layout',
  data() {
    return {
      isAuthenticated: false
    }
  },
  created() {
    // Простейшая проверка: если есть meta тег с именем user-id
    const userId = document.querySelector('meta[name="user-id"]');
    this.isAuthenticated = !!(userId && userId.content);
  },
  methods: {
    async submitLogout() {
      await axios.post('/logout');
      window.location.href = '/auth';
    }
  }
}
</script>
