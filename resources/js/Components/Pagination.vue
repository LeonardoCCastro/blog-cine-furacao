<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: {
    type: Array,
    required: true
  }
})
</script>

<template>
    <nav class="mt-8 sm:mt-12" aria-label="Page navigation example" v-if="links.length > 3">
        <ul class="flex items-center justify-center -space-x-px">
            <li v-for="(link, index) in links" :key="index" >
                <Link v-if="link.url" :href="link.url" preserve-scroll class="flex items-center justify-center px-3 h-8 leading-tight border" :class="[ link.active ? 'text-blue-600 border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white', index === 0 ? 'rounded-s-lg border-e-0' : '', index === links.length - 1 ? 'rounded-e-lg' : '' ]" v-html="link.label"/>
                <span v-else :class="[index === 0 ? 'rounded-s-lg border-e-0' : '', index === links.length - 1 ? 'rounded-e-lg' : '' ]" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed dark:bg-gray-700 dark:border-gray-700 dark:text-gray-500" v-html="link.label"/>
            </li>
        </ul>
    </nav>
</template>