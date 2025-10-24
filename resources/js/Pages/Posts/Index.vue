<script setup>
  import ApplicationLayout from '@/Layouts/ApplicationLayout.vue';
  defineProps({ 
    posts: {
      type: Array,
    },
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
  });
</script>

<template>
  <ApplicationLayout title="Index">
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-sm text-center lg:mb-16 mb-8">
            <h2 class="mb-4 text-3xl lg:text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Our Blog</h2>
            <p class="font-light text-gray-500 sm:text-xl dark:text-gray-400" >We use an agile approach to test assumptions and connect with the needs of your audience early and often.</p>
        </div> 
        <div class="grid gap-8 lg:grid-cols-2">
            <article v-for="post in posts" :key="post.id" class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-5 text-gray-500">
                    <span class="bg-primary-100 text-primary-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-primary-200 dark:text-primary-800">
                        <svg class="mr-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path></svg>
                        Tag
                    </span>
                    <span class="text-sm">{{ new Date(post.created_at).toLocaleDateString() }}</span>
                </div>
                <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><a :href="route('posts.show', post.slug)">{{ post.title }}</a></h2>
                <p class="mb-5 font-light text-gray-500 dark:text-gray-400" v-html="post.excerpt"></p>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-0">
                    <div class="flex items-center space-x-4 w-full sm:w-auto justify-start">
                        <img class="w-7 h-7 rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png" alt="Jese Leos avatar" />
                        <span class="font-medium dark:text-white">
                            {{ post.user?.name ?? 'Anônimo' }}
                        </span>
                    </div>
                    <a :href="route('posts.show', post.slug)" class="inline-flex items-center font-medium text-primary-600 dark:text-primary-500 hover:underline w-full sm:w-auto justify-end">
                        Read more
                        <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </a>
                </div>
            </article>                
        </div>  
        <div class="p-4 mx-auto mt-8 rounded-md lg:mt-16 bg-gray-50 dark:bg-gray-800">
            <div class="flex flex-col justify-end gap-3 md:gap-6 md:items-center md:flex-row">
                <a :href="route('posts.all')" title="Read more" class="inline-flex items-center text-base font-medium text-primary-700 hover:underline dark:text-primary-500">
                    Read more
                    <svg aria-hidden="true" class="w-4 h-4 ml-1.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
  </ApplicationLayout>
</template>
