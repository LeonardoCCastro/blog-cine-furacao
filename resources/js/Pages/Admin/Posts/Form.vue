<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  post: {
    type: Object,
    required: false,
    default: null,
  },
  submitUrl: {
    type: String,
    required: true,
  },
  method: {
    type: String,
    required: true,
  },
  buttonLabel: {
    type: String,
    required: true,
  },
});

const form = useForm({
  title: props.post?.title ?? '',
  excerpt: props.post?.excerpt ?? '',
  content: props.post?.content ?? '',
  published: Boolean(props.post?.published ?? false),
  image: null,
});

function submit() {
  if (props.method === 'post') {
    form.post(props.submitUrl, { forceFormData: true });
    return;
  }

  form
    .transform((data) => ({ ...data, _method: 'put' }))
    .post(props.submitUrl, { forceFormData: true });
}
</script>

<template>
  <form @submit.prevent="submit" class="space-y-5">
    <div>
      <label class="block mb-1 text-sm font-medium text-gray-700">Titulo</label>
      <input v-model="form.title" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
      <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
    </div>

    <div>
      <label class="block mb-1 text-sm font-medium text-gray-700">Resumo</label>
      <textarea v-model="form.excerpt" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
      <p v-if="form.errors.excerpt" class="mt-1 text-sm text-red-600">{{ form.errors.excerpt }}</p>
    </div>

    <div>
      <label class="block mb-1 text-sm font-medium text-gray-700">Conteudo</label>
      <textarea v-model="form.content" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
      <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
    </div>

    <div>
      <label class="block mb-1 text-sm font-medium text-gray-700">Imagem</label>
      <input type="file" accept="image/*" @input="form.image = $event.target.files[0]" />
      <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>

      <img
        v-if="post?.image_url"
        :src="post.image_url"
        alt="Imagem atual"
        class="object-cover w-48 h-32 mt-3 rounded"
      >
    </div>

    <div class="flex items-center gap-2">
      <input id="published" v-model="form.published" type="checkbox" class="rounded border-gray-300" />
      <label for="published" class="text-sm text-gray-700">Publicado</label>
    </div>

    <div>
      <button type="submit" :disabled="form.processing" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
        {{ buttonLabel }}
      </button>
    </div>
  </form>
</template>
