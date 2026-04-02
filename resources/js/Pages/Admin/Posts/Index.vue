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
    <Head title="Admin · Posts" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Posts</h1>
                <Link
                    :href="route('admin.posts.create')"
                    class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Novo post
                </Link>
            </div>
        </template>

        <section class="space-y-4">
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead
                            class="border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-400"
                        >
                            <tr>
                                <th class="px-5 py-3">Titulo</th>
                                <th class="px-5 py-3">Categoria</th>
                                <th class="px-5 py-3">Autor</th>
                                <th class="px-5 py-3">Metricas</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Data</th>
                                <th class="px-5 py-3 text-right">Acoes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr
                                v-for="post in posts.data"
                                :key="post.id"
                                class="hover:bg-gray-50/70 dark:hover:bg-gray-700/20"
                            >
                                <td class="px-5 py-4">
                                    <Link
                                        :href="route('admin.posts.edit', post.id)"
                                        class="font-medium text-gray-900 hover:underline dark:text-white"
                                    >
                                        {{ post.title }}
                                    </Link>
                                </td>
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                    {{ post.category?.name || '-' }}
                                    <span v-if="post.subcategory"> / {{ post.subcategory.name }}</span>
                                </td>
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ post.user?.name || '-' }}</td>
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                    {{ post.views }} views · {{ post.comments_count }} comentarios
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            post.published
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                                : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                        "
                                    >
                                        {{ post.published ? 'Publicado' : 'Rascunho' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                    {{ new Date(post.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <Link
                                            :href="route('admin.posts.edit', post.id)"
                                            class="font-medium text-blue-600 hover:underline"
                                            >Editar</Link
                                        >
                                        <button
                                            type="button"
                                            class="font-medium text-red-600 hover:underline"
                                            @click="destroyPost(post.id)"
                                        >
                                            Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="posts.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-5 py-8 text-center text-gray-500 dark:text-gray-400"
                                >
                                    Nenhum post encontrado.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination :links="posts.links" />
        </section>
    </AuthenticatedLayout>
</template>
