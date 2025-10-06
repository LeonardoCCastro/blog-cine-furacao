<script setup>
defineProps({ posts: Array })
</script>

<template>
  <div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Últimas Notícias</h1>
    <div class="grid md:grid-cols-2 gap-6">
      <div v-for="post in posts" :key="post.id" class="bg-white rounded shadow p-4">
        <h2 class="text-xl font-semibold mb-2">
          <a :href="`/posts/${post.slug}`">{{ post.title }}</a>
        </h2>
        <p class="text-gray-600 mb-2" v-html="post.excerpt"></p>
        <div class="text-sm text-gray-500">Por {{ post.user?.name ?? 'Anônimo' }} — {{ new Date(post.created_at).toLocaleDateString() }}</div>
      </div>
    </div>
  </div>
</template>
