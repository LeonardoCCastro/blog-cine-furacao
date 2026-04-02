<script setup>
import DisqusComments from '@/Components/DisqusComments.vue';
import ApplicationLayout from '@/Layouts/ApplicationLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    relatedPosts: {
        type: Array,
        default: () => [],
    },
    trendingPosts: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const disqusShortname = import.meta.env.VITE_DISQUS_SHORTNAME || '';
const disqusUrl = computed(() => route('posts.show', props.post.slug));
const disqusIdentifier = computed(() => `post-${props.post.id}`);
const articleUrl = computed(() => disqusUrl.value);
const articleCategories = computed(() => (page.props.blogCategories ?? []).slice(0, 8));
const copied = ref(false);

const readingTime = computed(() => {
    const plainText = props.post.content
        .replace(/<[^>]*>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    const words = plainText === '' ? 0 : plainText.split(' ').length;
    return Math.max(1, Math.ceil(words / 220));
});

const articleLead = computed(
    () => props.post.excerpt || 'Cobertura editorial do Cine Furacao com foco em cinema, series e cultura pop.',
);

function formatDate(value) {
    return new Date(value).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function formatDateTime(value) {
    return new Date(value).toLocaleString('pt-BR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function shareTo(platform) {
    const encodedUrl = encodeURIComponent(articleUrl.value);
    const encodedTitle = encodeURIComponent(props.post.title);

    const targets = {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
        x: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`,
        reddit: `https://www.reddit.com/submit?url=${encodedUrl}&title=${encodedTitle}`,
    };

    const target = targets[platform];
    if (!target) return;

    window.open(target, '_blank', 'noopener,noreferrer,width=720,height=640');
}

async function copyArticleLink() {
    try {
        await navigator.clipboard.writeText(articleUrl.value);
        copied.value = true;
        window.setTimeout(() => {
            copied.value = false;
        }, 2200);
    } catch {
        window.prompt('Copie o link do artigo:', articleUrl.value);
    }
}
</script>

<template>
    <ApplicationLayout :title="post.title">
        <section
            class="relative isolate overflow-hidden border-b border-gray-200 bg-gray-950 text-white dark:border-gray-800"
        >
            <div
                class="absolute inset-0 bg-cover bg-center"
                :style="{ backgroundImage: `url('${post.image_url}')` }"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-black/45 via-black/70 to-gray-950" />
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(220,38,38,0.35),_transparent_35%)]"
            />

            <div class="relative mx-auto max-w-7xl px-4 py-12 sm:py-16 lg:py-20">
                <div class="max-w-4xl">
                    <div
                        class="flex flex-wrap items-center gap-3 text-[11px] font-bold uppercase tracking-[0.3em] text-red-200"
                    >
                        <Link
                            v-if="post.category"
                            :href="route('posts.all', { category: post.category.slug })"
                            class="rounded-full border border-white/15 bg-white/10 px-3 py-1 transition hover:bg-white/20"
                        >
                            {{ post.category.name }}
                        </Link>
                        <Link
                            v-if="post.subcategory && post.category"
                            :href="
                                route('posts.all', { category: post.category.slug, subcategory: post.subcategory.slug })
                            "
                            class="rounded-full border border-white/15 bg-white/10 px-3 py-1 transition hover:bg-white/20"
                        >
                            {{ post.subcategory.name }}
                        </Link>
                        <span class="text-white/70">{{ readingTime }} min de leitura</span>
                    </div>

                    <h1
                        class="mt-5 max-w-5xl text-3xl font-black uppercase leading-tight tracking-tight text-white sm:text-4xl lg:text-6xl"
                    >
                        {{ post.title }}
                    </h1>

                    <p class="mt-5 max-w-3xl text-sm leading-7 text-gray-200 sm:text-base">
                        {{ articleLead }}
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-gray-200">
                        <span
                            v-if="post.user"
                            class="font-semibold uppercase tracking-[0.14em] text-white"
                            >{{ post.user.name }}</span
                        >
                        <time :datetime="post.created_at">{{ formatDateTime(post.created_at) }}</time>
                        <span>{{ post.views }} visualizacoes</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative z-10 mx-auto -mt-10 max-w-7xl px-4 pb-12 sm:-mt-14 sm:pb-16">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div
                    class="overflow-hidden rounded-[30px] border border-gray-200 bg-white shadow-[0_24px_70px_-40px_rgba(15,23,42,0.45)] dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700 sm:px-8">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600 dark:text-red-400"
                                >
                                    Materia
                                </p>
                                <div
                                    class="mt-3 flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    <span>{{ formatDate(post.created_at) }}</span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-300 dark:bg-gray-600" />
                                    <span>{{ readingTime }} min</span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-300 dark:bg-gray-600" />
                                    <span>{{ post.views }} views</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-600 transition hover:border-red-500 hover:text-red-600 dark:border-gray-600 dark:text-gray-300 dark:hover:border-red-400 dark:hover:text-red-400"
                                    @click="shareTo('facebook')"
                                >
                                    Facebook
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-600 transition hover:border-red-500 hover:text-red-600 dark:border-gray-600 dark:text-gray-300 dark:hover:border-red-400 dark:hover:text-red-400"
                                    @click="shareTo('x')"
                                >
                                    X
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-600 transition hover:border-red-500 hover:text-red-600 dark:border-gray-600 dark:text-gray-300 dark:hover:border-red-400 dark:hover:text-red-400"
                                    @click="shareTo('reddit')"
                                >
                                    Reddit
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-600 transition hover:border-red-500 hover:text-red-600 dark:border-gray-600 dark:text-gray-300 dark:hover:border-red-400 dark:hover:text-red-400"
                                    @click="copyArticleLink"
                                >
                                    {{ copied ? 'Link copiado' : 'Copiar link' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <article class="article-body px-6 py-8 sm:px-8 sm:py-10">
                        <div
                            class="prose prose-gray max-w-none dark:prose-invert"
                            v-html="post.content"
                        />
                    </article>
                </div>

                <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                    <div
                        class="rounded-[28px] border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600 dark:text-red-400">
                            Ficha editorial
                        </p>

                        <div class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-300">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gray-400">
                                    Publicado em
                                </p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                    {{ formatDateTime(post.created_at) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gray-400">Autor</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                    {{ post.user?.name || 'Redacao Cine Furacao' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gray-400">Categoria</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <Link
                                        v-if="post.category"
                                        :href="route('posts.all', { category: post.category.slug })"
                                        class="rounded-full bg-gray-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-white dark:bg-white dark:text-gray-900"
                                    >
                                        {{ post.category.name }}
                                    </Link>
                                    <Link
                                        v-if="post.subcategory && post.category"
                                        :href="
                                            route('posts.all', {
                                                category: post.category.slug,
                                                subcategory: post.subcategory.slug,
                                            })
                                        "
                                        class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-red-700 dark:bg-red-900/30 dark:text-red-300"
                                    >
                                        {{ post.subcategory.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="trendingPosts.length"
                        class="rounded-[28px] border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600 dark:text-red-400">
                                Em alta
                            </p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                                Mais lidas
                            </h2>
                        </div>

                        <div class="mt-4 space-y-4">
                            <article
                                v-for="(item, index) in trendingPosts"
                                :key="item.id"
                                class="border-b border-gray-200 pb-4 last:border-0 last:pb-0 dark:border-gray-700"
                            >
                                <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">
                                    0{{ index + 1 }}
                                </p>
                                <h3
                                    class="mt-2 text-lg font-black uppercase leading-tight text-gray-900 dark:text-white"
                                >
                                    <Link
                                        :href="route('posts.show', item.slug)"
                                        class="hover:text-red-600 dark:hover:text-red-400"
                                        >{{ item.title }}</Link
                                    >
                                </h3>
                                <p
                                    class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400"
                                >
                                    {{ item.category?.name || 'Cine Furacao' }} / {{ item.views }} views
                                </p>
                            </article>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden rounded-[28px] border border-gray-200 bg-gradient-to-br from-red-600 via-red-700 to-gray-950 p-6 text-white shadow-sm dark:border-gray-700"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-red-100">Explorar</p>
                        <h2 class="mt-3 text-3xl font-black uppercase leading-tight">
                            Continue navegando pelo arquivo do blog
                        </h2>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <Link
                                :href="route('posts.all')"
                                class="rounded-full bg-white px-5 py-2 text-sm font-semibold uppercase tracking-[0.16em] text-gray-900 transition hover:bg-gray-100"
                            >
                                Todas as postagens
                            </Link>
                            <Link
                                v-if="post.category"
                                :href="route('posts.all', { category: post.category.slug })"
                                class="rounded-full border border-white/35 px-5 py-2 text-sm font-semibold uppercase tracking-[0.16em] text-white transition hover:bg-white/10"
                            >
                                Mais de {{ post.category.name }}
                            </Link>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-12 sm:pb-16">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="space-y-8">
                    <section
                        v-if="relatedPosts.length"
                        class="rounded-[30px] border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8"
                    >
                        <div
                            class="mb-6 flex items-end justify-between gap-4 border-b border-gray-200 pb-4 dark:border-gray-700"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600 dark:text-red-400"
                                >
                                    Continue lendo
                                </p>
                                <h2
                                    class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white"
                                >
                                    Mais historias
                                </h2>
                            </div>
                            <Link
                                :href="route('posts.all')"
                                class="text-sm font-semibold uppercase tracking-[0.16em] text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400"
                            >
                                Ver arquivo
                            </Link>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <article
                                v-for="item in relatedPosts"
                                :key="item.id"
                                class="overflow-hidden rounded-[24px] border border-gray-200 bg-gray-50 transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900/60"
                            >
                                <Link
                                    :href="route('posts.show', item.slug)"
                                    class="block"
                                >
                                    <img
                                        :src="item.image_url"
                                        :alt="item.title"
                                        class="h-48 w-full object-cover"
                                    />
                                    <div class="p-5">
                                        <p
                                            class="text-[11px] font-bold uppercase tracking-[0.24em] text-red-600 dark:text-red-400"
                                        >
                                            {{ item.category?.name || 'Cine Furacao' }}
                                        </p>
                                        <h3
                                            class="mt-3 text-xl font-black uppercase leading-tight text-gray-900 dark:text-white"
                                        >
                                            {{ item.title }}
                                        </h3>
                                        <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                            {{ item.excerpt || 'Sem resumo cadastrado para esta postagem.' }}
                                        </p>
                                    </div>
                                </Link>
                            </article>
                        </div>
                    </section>

                    <section
                        class="rounded-[30px] border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8"
                    >
                        <div class="mb-6 border-b border-gray-200 pb-4 dark:border-gray-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600 dark:text-red-400">
                                Comentarios
                            </p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                                Discussao
                            </h2>
                        </div>

                        <DisqusComments
                            :shortname="disqusShortname"
                            :identifier="disqusIdentifier"
                            :url="disqusUrl"
                            :title="post.title"
                        />
                    </section>
                </div>

                <aside class="space-y-6">
                    <div
                        class="rounded-[28px] border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600 dark:text-red-400">
                            Editorias
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <Link
                                v-for="category in articleCategories"
                                :key="category.id"
                                :href="route('posts.all', { category: category.slug })"
                                class="rounded-full border border-gray-200 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] text-gray-600 transition hover:border-red-500 hover:text-red-600 dark:border-gray-600 dark:text-gray-300 dark:hover:border-red-400 dark:hover:text-red-400"
                            >
                                {{ category.name }}
                            </Link>
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    </ApplicationLayout>
</template>

<style scoped>
.article-body :deep(.prose) {
    color: rgb(31 41 55);
}

.article-body :deep(.prose h2),
.article-body :deep(.prose h3),
.article-body :deep(.prose h4) {
    font-weight: 900;
    letter-spacing: -0.02em;
    text-transform: uppercase;
}

.article-body :deep(.prose a) {
    color: rgb(220 38 38);
    font-weight: 600;
    text-decoration: none;
}

.article-body :deep(.prose a:hover) {
    text-decoration: underline;
}

.article-body :deep(.prose img) {
    border-radius: 1.5rem;
}

.article-body :deep(.prose figure) {
    margin-left: 0;
    margin-right: 0;
}

.article-body :deep(.prose figcaption) {
    text-align: left;
    font-size: 0.75rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.article-body :deep(.prose blockquote) {
    border-left-color: rgb(220 38 38);
    font-style: normal;
    font-weight: 600;
}

.dark .article-body :deep(.prose) {
    color: rgb(229 231 235);
}

.dark .article-body :deep(.prose strong),
.dark .article-body :deep(.prose h2),
.dark .article-body :deep(.prose h3),
.dark .article-body :deep(.prose h4) {
    color: rgb(255 255 255);
}
</style>
