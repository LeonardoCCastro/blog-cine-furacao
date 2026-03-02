<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
  posts: {
    type: Object,
    required: true,
  },
});

function destroyPost(id) {
  if (!window.confirm('Deseja excluir este post?')) {
    return;
  }

  router.delete(route('admin.posts.destroy', id));
}
</script>

<template>
  <Head title="Admin Posts" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Posts</h2>
        <Link
          :href="route('admin.posts.create')"
          class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
        >
          Novo post
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Titulo</th>
                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Autor</th>
                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data</th>
                <th class="px-4 py-3" />
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="post in posts.data" :key="post.id">
                <td class="px-4 py-3 font-medium text-gray-900">{{ post.title }}</td>
                <td class="px-4 py-3 text-gray-600">{{ post.user?.name || '-' }}</td>
                <td class="px-4 py-3">
                  <span :class="post.published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" class="px-2 py-1 text-xs font-semibold rounded">
                    {{ post.published ? 'Publicado' : 'Rascunho' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ new Date(post.created_at).toLocaleDateString() }}</td>
                <td class="px-4 py-3 space-x-3 text-right">
                  <Link :href="route('admin.posts.edit', post.id)" class="text-blue-600 hover:underline">Editar</Link>
                  <button type="button" class="text-red-600 hover:underline" @click="destroyPost(post.id)">Excluir</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="posts.links" class="mt-6" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>
