<script setup>
import { Head, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostForm from './Form.vue';

defineProps({
    post: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
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

onMounted(() => {
    if (!window.location.hash.startsWith('#comment-')) {
        return;
    }

    const target = document.querySelector(window.location.hash);
    if (!target) {
        return;
    }

    target.classList.add('ring-2', 'ring-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
});
</script>

<template>
    <Head :title="`Editar: ${post.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Editar post</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Atualize o conteudo e gerencie comentarios desta postagem.
                </p>
            </div>
        </template>

        <section class="mx-auto max-w-7xl space-y-6">
            <PostForm
                method="put"
                :post="post"
                :categories="categories"
                :submit-url="route('admin.posts.update', post.id)"
                button-label="Salvar alteracoes"
            />

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Comentarios da postagem</h2>
                </div>

                <div class="space-y-3 p-5">
                    <article
                        v-for="comment in post.comments"
                        :key="comment.id"
                        :id="`comment-${comment.id}`"
                        class="flex items-start justify-between gap-4 rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                    >
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ comment.name }} · {{ new Date(comment.created_at).toLocaleDateString() }}
                            </p>
                            <p class="mt-1 whitespace-pre-line text-gray-800 dark:text-gray-200">
                                {{ comment.content }}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="text-sm font-medium text-red-600 hover:underline"
                            @click="destroyComment(post.id, comment.id)"
                        >
                            Excluir
                        </button>
                    </article>

                    <p
                        v-if="post.comments.length === 0"
                        class="text-sm text-gray-500 dark:text-gray-400"
                    >
                        Esta postagem ainda nao possui comentarios.
                    </p>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
