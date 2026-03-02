<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  featuredPosts: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <Head title="Admin Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        <div class="flex gap-2">
          <Link
            :href="route('admin.posts.index')"
            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Ver todos os posts
          </Link>
          <Link
            :href="route('admin.posts.create')"
            class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
          >
            Novo post
          </Link>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 bg-white rounded-lg shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900">6 posts principais</h3>
          <ul class="mt-4 space-y-3">
            <li v-for="post in featuredPosts" :key="post.id" class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">{{ post.title }}</p>
                <p class="text-sm text-gray-500">{{ new Date(post.created_at).toLocaleDateString() }}</p>
              </div>
              <span
                :class="post.published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                class="px-2 py-1 text-xs font-semibold rounded"
              >
                {{ post.published ? 'Publicado' : 'Rascunho' }}
              </span>
            </li>
          </ul>
          <p v-if="featuredPosts.length === 0" class="text-sm text-gray-500">
            Nenhum post publicado ainda.
          </p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
