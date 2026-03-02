<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostForm from './Form.vue';

defineProps({
  post: {
    type: Object,
    required: true,
  },
});

function destroyComment(postId, commentId) {
  if (!window.confirm('Deseja excluir este comentario?')) {
    return;
  }

  router.delete(route('admin.posts.comments.destroy', { post: postId, comment: commentId }), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head :title="`Editar: ${post.title}`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar post</h2>
    </template>

    <div class="py-8">
      <div class="px-4 mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="p-6 bg-white rounded-lg shadow-sm mb-6">
          <PostForm
            method="put"
            :post="post"
            :submit-url="route('admin.posts.update', post.id)"
            button-label="Salvar alteracoes"
          />
        </div>

        <div class="p-6 bg-white rounded-lg shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900">Comentarios da postagem</h3>

          <div class="mt-4 space-y-3">
            <article
              v-for="comment in post.comments"
              :key="comment.id"
              class="flex items-start justify-between gap-4 p-4 border border-gray-200 rounded-lg"
            >
              <div>
                <p class="text-sm text-gray-500">
                  {{ comment.name }} • {{ new Date(comment.created_at).toLocaleDateString() }}
                </p>
                <p class="mt-1 text-gray-800 whitespace-pre-line">{{ comment.content }}</p>
              </div>

              <button
                type="button"
                class="text-sm font-medium text-red-600 hover:underline"
                @click="destroyComment(post.id, comment.id)"
              >
                Excluir
              </button>
            </article>

            <p v-if="post.comments.length === 0" class="text-sm text-gray-500">
              Esta postagem ainda nao possui comentarios.
            </p>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
