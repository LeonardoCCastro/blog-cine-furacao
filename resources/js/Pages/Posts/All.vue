<script setup>
import ApplicationLayout from '@/Layouts/ApplicationLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    posts: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Array,
        required: true,
    },
    trendingPosts: {
        type: Array,
        required: true,
    },
});

const search = ref(props.filters.search ?? '');
const category = ref(props.filters.category ?? '');
const subcategory = ref(props.filters.subcategory ?? '');

const selectedCategory = computed(() => props.categories.find((item) => item.slug === category.value) ?? null);
const availableSubcategories = computed(() => selectedCategory.value?.subcategories ?? []);
const archiveLead = computed(() => props.posts.data[0] ?? null);
const archiveFeed = computed(() => props.posts.data.slice(1));
const activeFiltersCount = computed(() => [search.value, category.value, subcategory.value].filter(Boolean).length);

watch([search, category, subcategory], () => {
    router.get(
        route('posts.all'),
        {
            search: search.value || undefined,
            category: category.value || undefined,
            subcategory: subcategory.value || undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
});

watch(category, () => {
    const hasSubcategory = availableSubcategories.value.some((item) => item.slug === subcategory.value);
    if (!hasSubcategory) {
        subcategory.value = '';
    }
});

function formatDate(value) {
    return new Date(value).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function clearFilters() {
    search.value = '';
    category.value = '';
    subcategory.value = '';
}
</script>

<template>
    <ApplicationLayout title="Todas as postagens">
        <section
            class="border-b border-gray-200 bg-gradient-to-b from-gray-50 via-white to-white dark:border-gray-700 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900"
        >
            <div class="mx-auto max-w-7xl px-4 py-8 sm:py-10">
                <div
                    class="mb-8 flex flex-col gap-4 border-b border-gray-200 pb-5 dark:border-gray-700 lg:flex-row lg:items-end lg:justify-between"
                >
                    <div class="max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.34em] text-red-600">Arquivo editorial</p>
                        <h1
                            class="mt-3 text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-5xl"
                        >
                            Todas as postagens em um arquivo visualmente alinhado com a home
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                            Busca, filtros e paginação continuam no centro do fluxo, mas agora com a mesma linguagem
                            editorial da capa do blog.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button
                            v-if="activeFiltersCount"
                            type="button"
                            class="inline-flex items-center rounded-full border border-gray-300 px-4 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-gray-700 transition hover:border-red-500 hover:text-red-600 dark:border-gray-600 dark:text-gray-200 dark:hover:border-red-500 dark:hover:text-red-400"
                            @click="clearFilters"
                        >
                            Limpar filtros
                        </button>
                        <span
                            class="inline-flex items-center rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-white dark:bg-white dark:text-gray-900"
                        >
                            {{ posts.total }} postagens
                        </span>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.85fr)]">
                    <article
                        v-if="archiveLead"
                        class="group overflow-hidden rounded-[28px] border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <Link
                            :href="route('posts.show', archiveLead.slug)"
                            class="block"
                        >
                            <div class="relative min-h-[320px] overflow-hidden sm:min-h-[420px]">
                                <img
                                    :src="archiveLead.image_url"
                                    :alt="archiveLead.title"
                                    class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent" />
                                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-8">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.32em] text-red-300">
                                        {{ archiveLead.category?.name || 'Sem categoria' }}
                                        <span v-if="archiveLead.subcategory">
                                            / {{ archiveLead.subcategory.name }}</span
                                        >
                                    </p>
                                    <h2
                                        class="mt-3 max-w-3xl text-3xl font-black uppercase leading-tight text-white sm:text-5xl"
                                    >
                                        {{ archiveLead.title }}
                                    </h2>
                                    <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-200">
                                        {{ archiveLead.excerpt || 'Sem resumo cadastrado para esta postagem.' }}
                                    </p>
                                    <p class="mt-4 text-xs font-semibold uppercase tracking-[0.22em] text-gray-300">
                                        {{ formatDate(archiveLead.created_at) }}
                                        <span v-if="archiveLead.user"> / {{ archiveLead.user.name }}</span>
                                        / {{ archiveLead.views }} views
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </article>

                    <div
                        class="rounded-[28px] border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="border-b border-gray-200 pb-4 dark:border-gray-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600">Filtros</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                                Refine o arquivo
                            </h2>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div>
                                <label
                                    class="mb-2 block text-[11px] font-bold uppercase tracking-[0.22em] text-gray-400"
                                    >Busca</label
                                >
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Buscar titulo, resumo ou conteudo"
                                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-red-500 dark:focus:ring-red-900/30"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-[11px] font-bold uppercase tracking-[0.22em] text-gray-400"
                                    >Categoria</label
                                >
                                <select
                                    v-model="category"
                                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-red-500 dark:focus:ring-red-900/30"
                                >
                                    <option value="">Todas as categorias</option>
                                    <option
                                        v-for="item in categories"
                                        :key="item.id"
                                        :value="item.slug"
                                    >
                                        {{ item.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-[11px] font-bold uppercase tracking-[0.22em] text-gray-400"
                                    >Tag</label
                                >
                                <select
                                    v-model="subcategory"
                                    :disabled="!category"
                                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-red-500 dark:focus:ring-red-900/30"
                                >
                                    <option value="">Todas as tags</option>
                                    <option
                                        v-for="item in availableSubcategories"
                                        :key="item.id"
                                        :value="item.slug"
                                    >
                                        {{ item.name }}
                                    </option>
                                </select>
                            </div>

                            <div
                                v-if="selectedCategory || activeFiltersCount"
                                class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-900"
                            >
                                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gray-400">
                                    Estado atual
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span
                                        v-if="search"
                                        class="rounded-full bg-gray-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-white dark:bg-white dark:text-gray-900"
                                    >
                                        {{ search }}
                                    </span>
                                    <span
                                        v-if="selectedCategory"
                                        class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-red-700 dark:bg-red-900/30 dark:text-red-300"
                                    >
                                        {{ selectedCategory.name }}
                                    </span>
                                    <span
                                        v-if="subcategory"
                                        class="rounded-full bg-gray-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                    >
                                        {{ availableSubcategories.find((item) => item.slug === subcategory)?.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <Link
                        :href="route('posts.all')"
                        class="rounded-full border border-red-600 px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-red-600 transition hover:bg-red-50 dark:border-red-500 dark:text-red-400 dark:hover:bg-red-900/20"
                    >
                        Arquivo completo
                    </Link>
                    <Link
                        v-for="item in categories.slice(0, 8)"
                        :key="item.id"
                        :href="route('posts.all', { category: item.slug })"
                        class="rounded-full border border-transparent bg-gray-100 px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-gray-700 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        {{ item.name }}
                    </Link>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-10 px-4 py-10 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div>
                <div class="flex items-end justify-between gap-4 border-b-2 border-gray-900 pb-3 dark:border-white">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-red-600">Arquivo</p>
                        <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                            Listagem completa
                        </h2>
                    </div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">
                        Pagina {{ posts.current_page }} de {{ posts.last_page }}
                    </p>
                </div>

                <div
                    v-if="posts.data.length"
                    class="mt-6 divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <article
                        v-for="post in archiveFeed"
                        :key="post.id"
                        class="grid gap-4 py-6 sm:grid-cols-[220px_minmax(0,1fr)]"
                    >
                        <Link
                            :href="route('posts.show', post.slug)"
                            class="block overflow-hidden rounded-2xl bg-gray-200"
                        >
                            <img
                                :src="post.image_url"
                                :alt="post.title"
                                class="h-full min-h-[150px] w-full object-cover transition duration-500 hover:scale-105"
                            />
                        </Link>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400"
                            >
                                {{ formatDate(post.created_at) }}
                                <span v-if="post.category"> / {{ post.category.name }}</span>
                                <span v-if="post.subcategory"> / {{ post.subcategory.name }}</span>
                            </p>
                            <h2 class="mt-2 text-2xl font-black uppercase leading-tight text-gray-900 dark:text-white">
                                <Link
                                    :href="route('posts.show', post.slug)"
                                    class="hover:text-red-600 dark:hover:text-red-400"
                                    >{{ post.title }}</Link
                                >
                            </h2>
                            <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                {{ post.excerpt || 'Sem resumo cadastrado para esta postagem.' }}
                            </p>
                            <p
                                class="mt-4 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400"
                            >
                                {{ post.user?.name || 'Redacao Cine Furacao' }} / {{ post.views }} views
                            </p>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="mt-6 rounded-[28px] border border-dashed border-gray-300 bg-white px-6 py-12 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600">Sem resultados</p>
                    <h2 class="mt-3 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                        Nenhuma postagem encontrada
                    </h2>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                        Ajuste os filtros ou limpe a busca para voltar ao arquivo completo do blog.
                    </p>
                </div>

                <Pagination
                    :links="posts.links"
                    class="mt-8"
                />
            </div>

            <aside>
                <div class="space-y-6 lg:sticky lg:top-24">
                    <div
                        class="rounded-[28px] border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="border-b-2 border-gray-900 pb-3 dark:border-white">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-red-600">Trending</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                                Mais vistas do dia
                            </h2>
                        </div>

                        <div class="mt-4 space-y-4">
                            <article
                                v-for="(post, index) in trendingPosts"
                                :key="post.id"
                                class="border-b border-gray-200 pb-4 last:border-0 last:pb-0 dark:border-gray-700"
                            >
                                <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">
                                    0{{ index + 1 }}
                                </p>
                                <h3
                                    class="mt-2 text-lg font-black uppercase leading-tight text-gray-900 dark:text-white"
                                >
                                    <Link
                                        :href="route('posts.show', post.slug)"
                                        class="hover:text-red-600 dark:hover:text-red-400"
                                        >{{ post.title }}</Link
                                    >
                                </h3>
                                <p
                                    class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400"
                                >
                                    {{ post.category?.name || 'Sem categoria' }} / {{ post.views }} views
                                </p>
                            </article>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden rounded-[28px] border border-gray-200 bg-gradient-to-br from-red-600 via-red-700 to-gray-900 p-6 text-white shadow-sm dark:border-gray-700"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-red-100">Boletim</p>
                        <h2 class="mt-3 text-3xl font-black uppercase leading-tight">
                            Explore por editoria e acompanhe o ritmo do Cine Furacao
                        </h2>
                        <p class="mt-3 text-sm leading-6 text-red-50/90">
                            A listagem geral agora conversa visualmente com a home, mas sem abrir mao do papel de
                            arquivo filtravel.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <Link
                                :href="route('posts.index')"
                                class="inline-flex rounded-full bg-white px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-gray-900 transition hover:bg-gray-100"
                            >
                                Voltar para home
                            </Link>
                            <Link
                                v-if="selectedCategory"
                                :href="route('posts.all', { category: selectedCategory.slug })"
                                class="inline-flex rounded-full border border-white/40 px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-white/10"
                            >
                                Foco em {{ selectedCategory.name }}
                            </Link>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </ApplicationLayout>
</template>
