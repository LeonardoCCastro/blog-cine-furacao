<script setup>
import ApplicationLayout from '@/Layouts/ApplicationLayout.vue';

defineProps({
  posts: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <ApplicationLayout title="Blog">
    <section class="max-w-6xl px-4 py-10 mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Ultimos posts</h1>
      <p class="mt-2 text-gray-600 dark:text-gray-300">Confira os 6 posts mais recentes publicados.</p>

      <div class="grid gap-6 mt-8 md:grid-cols-2">
        <article
          v-for="post in posts"
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

      <div class="flex justify-center mt-10">
        <Link
          :href="route('posts.all')"
          class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
        >
          Mostrar mais
        </Link>
      </div>
    </section>
  </ApplicationLayout>
</template>
