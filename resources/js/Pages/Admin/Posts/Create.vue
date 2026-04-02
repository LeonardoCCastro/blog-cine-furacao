<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostForm from './Form.vue';

defineProps({
    categories: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Novo post" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Criar post</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Publique conteudos com o editor completo do blog.
                </p>
            </div>
        </template>

        <section class="mx-auto max-w-7xl">
            <PostForm
                method="post"
                :categories="categories"
                :submit-url="route('admin.posts.store')"
                button-label="Criar post"
            />
        </section>
    </AuthenticatedLayout>
</template>
