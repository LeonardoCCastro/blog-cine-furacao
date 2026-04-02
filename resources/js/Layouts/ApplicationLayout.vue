<script setup>
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Blog Cine Furacao',
    },
});
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
        <Navbar
            :can-login="$page.props.canLogin"
            :can-register="$page.props.canRegister"
            :categories="$page.props.blogCategories || []"
        />

        <main class="relative">
            <div
                class="absolute inset-x-0 top-0 -z-10 h-56 bg-gradient-to-b from-blue-50 to-transparent dark:from-gray-800/60"
            />
            <slot />
        </main>

        <Footer
            :can-login="$page.props.canLogin"
            :can-register="$page.props.canRegister"
            :laravel-version="$page.props.laravelVersion"
            :php-version="$page.props.phpVersion"
        />
    </div>
</template>
