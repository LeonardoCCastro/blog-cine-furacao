<script setup>
defineProps({ post: Object })
</script>

<template>
  <div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-4">{{ post.title }}</h1>
    <p class="text-sm text-gray-500 mb-6">Por {{ post.user?.name ?? 'Anônimo' }} — {{ new Date(post.created_at).toLocaleDateString() }}</p>
    <div class="prose max-w-none" v-html="post.content"></div>
  </div>
</template>
