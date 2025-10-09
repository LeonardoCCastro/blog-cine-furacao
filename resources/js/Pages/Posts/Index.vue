<script setup>
import { Head, Link } from '@inertiajs/vue3';
defineProps({ 
  posts: {
    type: Array,
  },
  canLogin: {
      type: Boolean,
  },
  canRegister: {
      type: Boolean,
  },
  laravelVersion: {
      type: String,
      required: true,
  },
  phpVersion: {
      type: String,
      required: true,
  },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
  
  <nav class="border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
      <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
          <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
          <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Flowbite</span>
      </a>
      <button data-collapse-toggle="navbar-solid-bg" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-solid-bg" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
          </svg>
      </button>
      <div class="hidden w-full md:block md:w-auto" id="navbar-solid-bg">
        <ul v-if="canLogin" class="flex flex-col font-medium mt-4 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent dark:bg-gray-800 md:dark:bg-transparent dark:border-gray-700">
          <li v-if="$page.props.auth.user">
            <a :href="route('dashboard')" class="block py-2 px-3 md:p-0 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:dark:text-blue-500 dark:bg-blue-600 md:dark:bg-transparent" aria-current="page">Login</a>
          </li>
          <div v-else class="flex flex-col md:flex-row md:space-x-8 rtl:space-x-reverse">         
            <li>
              <a :href="route('login')" class="block py-2 px-3 md:p-0 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:dark:text-blue-500 dark:bg-blue-600 md:dark:bg-transparent" aria-current="page">Login</a>
            </li>
            <li v-if="canRegister">
              <a :href="route('register')" class="block py-2 px-3 md:p-0 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Register</a>
            </li>
          </div>
        </ul>
      </div>
    </div>
  </nav>

  <main>
    <div class="max-w-5xl mx-auto py-8 px-4">
      <h1 class="text-3xl font-bold mb-6">Últimas Notícias</h1>
      <div class="grid gap-6">
        <div v-for="post in posts" :key="post.id" class="bg-white rounded shadow p-4">
          <h2 class="text-xl font-semibold mb-2">
            <a :href="`/posts/${post.slug}`">{{ post.title }}</a>
          </h2>
          <p class="text-gray-600 mb-2" v-html="post.excerpt"></p>
          <div class="text-sm text-gray-500">Por {{ post.user?.name ?? 'Anônimo' }} — {{ new Date(post.created_at).toLocaleDateString() }}</div>
        </div>
      </div>
    </div>
  </main>
  
</template>
