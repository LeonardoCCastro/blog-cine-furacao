<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostForm from './Form.vue';
</script>

<template>
  <Head title="Novo Post" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Novo post</h2>
    </template>

    <div class="py-8">
      <div class="px-4 mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="p-6 bg-white rounded-lg shadow-sm">
          <PostForm
            method="post"
            :submit-url="route('admin.posts.store')"
            button-label="Criar post"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
