<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import ApplicationLayout from '@/Layouts/ApplicationLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  posts: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    required: false,
    default: () => ({}),
  },
});

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
  router.get(route('posts.all'), { search: value || undefined }, {
    preserveState: true,
    replace: true,
  });
});
</script>

<template>
  <ApplicationLayout title="Todas as postagens">
    <section class="max-w-6xl px-4 py-10 mx-auto">
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Todas as postagens</h1>
          <p class="mt-2 text-gray-600 dark:text-gray-300">Navegue por todos os posts com filtros e paginacao.</p>
        </div>

        <div class="w-full md:max-w-sm">
          <label for="search" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar posts</label>
          <input
            id="search"
            v-model="search"
            type="text"
            placeholder="Buscar por titulo, resumo ou conteudo"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-white"
          >
        </div>
      </div>

      <div class="grid gap-6 mt-8 md:grid-cols-2">
        <article
          v-for="post in posts.data"
          :key="post.id"
          class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700"
        >
          <img
            v-if="post.image_url"
            :src="post.image_url"
            :alt="post.title"
            class="object-cover w-full h-48"
          >
          <div class="p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ new Date(post.created_at).toLocaleDateString() }}
              <span v-if="post.user">• {{ post.user.name }}</span>
            </p>
            <h2 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">
              <Link :href="route('posts.show', post.slug)">{{ post.title }}</Link>
            </h2>
            <p class="mt-3 text-gray-600 dark:text-gray-300">{{ post.excerpt || 'Sem resumo.' }}</p>
            <Link
              :href="route('posts.show', post.slug)"
              class="inline-block mt-4 text-sm font-medium text-blue-600 hover:underline"
            >
              Ler post
            </Link>
          </div>
        </article>
      </div>

      <p v-if="posts.data.length === 0" class="mt-8 text-sm text-gray-500 dark:text-gray-400">
        Nenhum post encontrado para o filtro atual.
      </p>

      <Pagination :links="posts.links" class="mt-8" />
    </section>
  </ApplicationLayout>
</template>
