<script setup>
import ApplicationLayout from '@/Layouts/ApplicationLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
  post: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  name: '',
  content: '',
});

function submit(postId) {
  form.post(route('posts.comments.store', postId), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
}
</script>

<template>
  <ApplicationLayout :title="post.title">
    <section class="max-w-4xl px-4 py-10 mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ post.title }}</h1>
      <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
        {{ new Date(post.created_at).toLocaleDateString() }}
        <span v-if="post.user">• {{ post.user.name }}</span>
      </p>

      <img
        v-if="post.image_url"
        :src="post.image_url"
        :alt="post.title"
        class="object-cover w-full mt-6 rounded-lg max-h-[420px]"
      >

      <div class="mt-8 prose prose-gray max-w-none dark:prose-invert" v-html="post.content" />

      <div class="mt-12">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Comentarios</h2>

        <form @submit.prevent="submit(post.id)" class="p-4 mt-4 space-y-4 bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
          <div>
            <label for="name" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            >
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <div>
            <label for="content" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Comentario</label>
            <textarea
              id="content"
              v-model="form.content"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            />
            <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
          >
            Enviar comentario
          </button>
        </form>

        <div class="mt-6 space-y-4">
          <article
            v-for="comment in post.comments"
            :key="comment.id"
            class="p-4 bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700"
          >
            <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ comment.name }} • {{ new Date(comment.created_at).toLocaleDateString() }}
            </p>
            <p class="mt-2 text-gray-700 whitespace-pre-line dark:text-gray-200">{{ comment.content }}</p>
          </article>

          <p v-if="post.comments.length === 0" class="text-gray-500 dark:text-gray-400">Nenhum comentario ainda.</p>
        </div>
      </div>
    </section>
  </ApplicationLayout>
</template>
