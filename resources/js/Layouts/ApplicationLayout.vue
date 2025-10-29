<script setup>
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  title: {
    type: String,
    required: false,
    default: 'Blog Furacão'
  }
});
</script>

<template>
    <Head :title="title" />
    <Navbar :canLogin="$page.props.canLogin" :canRegister="$page.props.canRegister"/>
    <main class="bg-white dark:bg-gray-900">
        <slot />
    </main>
    <Footer :canLogin="$page.props.canLogin" :canRegister="$page.props.canRegister" :laravelVersion="$page.props.laravelVersion" :phpVersion="$page.props.phpVersion"/>
</template>