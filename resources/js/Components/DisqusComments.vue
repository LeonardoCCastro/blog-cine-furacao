<script setup>
import { computed, onBeforeUnmount, onMounted, watch } from 'vue';

const props = defineProps({
    shortname: {
        type: String,
        default: '',
    },
    identifier: {
        type: [String, Number],
        required: true,
    },
    url: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    locale: {
        type: String,
        default: 'pt_BR',
    },
});

const isConfigured = computed(() => props.shortname.trim() !== '');

function buildConfig() {
    return function disqusConfig() {
        this.page.url = props.url;
        this.page.identifier = String(props.identifier);
        this.page.title = props.title;
        this.language = props.locale;
    };
}

function loadDisqus() {
    if (!isConfigured.value || typeof window === 'undefined') {
        return;
    }

    window.disqus_config = buildConfig();

    if (window.DISQUS) {
        window.DISQUS.reset({
            reload: true,
            config: window.disqus_config,
        });

        return;
    }

    const script = document.createElement('script');
    script.src = `https://${props.shortname}.disqus.com/embed.js`;
    script.setAttribute('data-timestamp', String(Date.now()));
    script.async = true;
    document.body.appendChild(script);
}

watch(
    () => [props.shortname, props.identifier, props.url, props.title],
    () => {
        loadDisqus();
    },
);

onMounted(() => {
    loadDisqus();
});

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        delete window.disqus_config;
    }
});
</script>

<template>
    <div class="mt-5">
        <div
            v-if="isConfigured"
            id="disqus_thread"
        />

        <div
            v-else
            class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-5 text-sm text-gray-600 dark:border-gray-600 dark:bg-gray-900/40 dark:text-gray-300"
        >
            Configure <code>VITE_DISQUS_SHORTNAME</code> no ambiente para ativar os comentarios via Disqus.
        </div>
    </div>
</template>
