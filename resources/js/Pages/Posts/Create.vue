<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import ApplicationLayout from '@/Layouts/ApplicationLayout.vue'
import Alert from '@/Components/Alert.vue'

const page = usePage();
const form = useForm({
  title: '',
  excerpt: '',
  content: '',
  cover_image: null,
})

function submit() {
  page.props.flash.success = null
  page.props.flash.error = null
  form.post(route('posts.store'), {
    forceFormData: true, 
    onSuccess: () => {
      form.reset()
    },
  })
}
</script>

<template>
  <ApplicationLayout title="Create">
    <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" />
    <Alert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" />
    <section class="bg-white dark:bg-gray-900">
      <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
          <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Novo Post</h2>
          <form @submit.prevent="submit">
              <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                  <div class="sm:col-span-2">
                      <label for="post-title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título</label>
                      <input type="text" name="post-title" id="post-title" v-model="form.title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Título" maxlength="255" required>
                      <span class="block mt-2 text-sm font-medium text-red-500">{{ form.errors.title }}</span>
                  </div>
                  <div class="sm:col-span-2">
                    <label for="post-excerpt" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Resumo</label>
                    <textarea id="post-excerpt" name="post-excerpt" rows="8" v-model="form.excerpt" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Resumo" maxlength="500"></textarea>
                    <span class="block mt-2 text-sm font-medium text-red-500">{{ form.errors.excerpt }}</span>
                  </div>     
                  <div class="sm:col-span-2">
                    <label for="post-content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Post</label>
                    <textarea id="post-content" name="post-content" rows="8" v-model="form.content" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Post" required></textarea>
                    <span class="block mt-2 text-sm font-medium text-red-500">{{ form.errors.content }}</span>
                  </div>    
                  <div class="sm:col-span-2">
                    <label for="post-cover-image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Imagem</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="post-cover-image" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                            </div>
                            <input id="post-cover-image" name="post-cover-image" type="file" @input="form.cover_image = $event.target.files[0]" class="hidden" />
                        </label>
                    </div> 
                    <span class="block mt-2 text-sm font-medium text-red-500">{{ form.errors.cover_image }}</span>
                  </div>
              </div>
              <button type="submit" :disabled="form.processing" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">
                  Criar post
              </button>
          </form>
      </div>
    </section>
  </ApplicationLayout>
</template>
