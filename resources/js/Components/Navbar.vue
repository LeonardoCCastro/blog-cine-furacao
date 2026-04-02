<script setup>
import { useTheme } from '@/Composables/useTheme';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    canLogin: {
        type: Boolean,
        default: false,
    },
    canRegister: {
        type: Boolean,
        default: false,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const search = ref('');
const isOpen = ref(false);
const { isDark, toggleTheme } = useTheme();

const featuredCategories = computed(() => {
    const preferredOrder = ['noticias', 'review'];
    const preferred = preferredOrder
        .map((slug) => props.categories.find((category) => category.slug === slug))
        .filter(Boolean);
    const remaining = props.categories.filter((category) => !preferredOrder.includes(category.slug)).slice(0, 3);

    return [...preferred, ...remaining];
});
const allSubcategories = computed(() =>
    props.categories.flatMap((category) =>
        (category.subcategories ?? []).map((subcategory) => ({
            id: subcategory.id,
            name: subcategory.name,
            slug: subcategory.slug,
            categoryName: category.name,
            categorySlug: category.slug,
        })),
    ),
);

function performSearch() {
    router.get(route('posts.all'), { search: search.value || undefined }, { preserveState: true });
}
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-gray-200 bg-white/85 backdrop-blur dark:border-gray-700 dark:bg-gray-900/85"
    >
        <nav class="max-w-screen-xl px-4 py-3 mx-auto">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <Link
                        :href="route('posts.index')"
                        class="text-xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Cine Furacao
                    </Link>

                    <div class="items-center hidden gap-4 lg:flex">
                        <Link
                            :href="route('posts.all')"
                            class="text-sm font-medium text-gray-700 hover:text-blue-700 dark:text-gray-200 dark:hover:text-blue-400"
                        >
                            TODAS AS POSTAGENS
                        </Link>
                        <Link
                            v-for="category in featuredCategories"
                            :key="category.id"
                            :href="route('posts.all', { category: category.slug })"
                            class="text-sm font-medium uppercase tracking-wide text-gray-600 hover:text-blue-700 dark:text-gray-300 dark:hover:text-blue-400"
                        >
                            {{ category.name }}
                        </Link>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="items-center hidden md:flex">
                        <label
                            for="search-posts"
                            class="sr-only"
                            >Buscar posts</label
                        >
                        <input
                            id="search-posts"
                            v-model="search"
                            @keyup.enter="performSearch"
                            type="text"
                            placeholder="Buscar posts..."
                            class="w-56 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        />
                    </div>

                    <button
                        type="button"
                        @click="toggleTheme"
                        class="rounded-lg border border-gray-300 p-2 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                    >
                        <span class="sr-only">Alternar tema</span>
                        <svg
                            v-if="isDark"
                            class="w-6 h-6 text-gray-800 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M13 3a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V3ZM6.343 4.929A1 1 0 0 0 4.93 6.343l1.414 1.414a1 1 0 0 0 1.414-1.414L6.343 4.929Zm12.728 1.414a1 1 0 0 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 1.414 1.414l1.414-1.414ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-9 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H3Zm16 0a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2ZM7.757 17.657a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414Zm9.9-1.414a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM13 19a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <svg
                            v-else
                            class="w-6 h-6 text-gray-800 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M11.675 2.015a.998.998 0 0 0-.403.011C6.09 2.4 2 6.722 2 12c0 5.523 4.477 10 10 10 4.356 0 8.058-2.784 9.43-6.667a1 1 0 0 0-1.02-1.33c-.08.006-.105.005-.127.005h-.001l-.028-.002A5.227 5.227 0 0 0 20 14a8 8 0 0 1-8-8c0-.952.121-1.752.404-2.558a.996.996 0 0 0 .096-.428V3a1 1 0 0 0-.825-.985Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>

                    <div
                        class="items-center hidden gap-2 md:flex"
                        v-if="canLogin"
                    >
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('admin.dashboard')"
                            class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Painel
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                            >
                                Login
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700"
                            >
                                Criar conta
                            </Link>
                        </template>
                    </div>

                    <button
                        type="button"
                        class="rounded-lg border border-gray-300 p-2 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800 lg:hidden"
                        @click="isOpen = !isOpen"
                    >
                        <span class="sr-only">Abrir menu</span>
                        <svg
                            class="w-5 h-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1Zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1Zm1 4a1 1 0 100 2h12a1 1 0 100-2H4Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div
                v-if="isOpen"
                class="pt-4 mt-4 space-y-4 border-t border-gray-200 lg:hidden dark:border-gray-700"
            >
                <input
                    v-model="search"
                    @keyup.enter="performSearch"
                    type="text"
                    placeholder="Buscar posts..."
                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                />

                <div class="flex flex-wrap gap-2">
                    <Link
                        :href="route('posts.all')"
                        class="rounded-full border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200"
                    >
                        Todas
                    </Link>
                    <Link
                        v-for="category in featuredCategories"
                        :key="`mobile-featured-${category.id}`"
                        :href="route('posts.all', { category: category.slug })"
                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700 dark:bg-blue-900/20 dark:text-blue-300"
                    >
                        {{ category.name }}
                    </Link>
                    <Link
                        v-for="category in categories"
                        :key="`mobile-cat-${category.id}`"
                        :href="route('posts.all', { category: category.slug })"
                        class="rounded-full border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200"
                    >
                        {{ category.name }}
                    </Link>
                </div>

                <div
                    v-if="allSubcategories.length"
                    class="flex flex-wrap gap-2"
                >
                    <Link
                        v-for="subcategory in allSubcategories"
                        :key="`mobile-sub-${subcategory.id}`"
                        :href="
                            route('posts.all', { category: subcategory.categorySlug, subcategory: subcategory.slug })
                        "
                        class="rounded-full bg-gray-100 px-2 py-1 text-[11px] font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300"
                    >
                        {{ subcategory.categoryName }} / {{ subcategory.name }}
                    </Link>
                </div>

                <div
                    class="flex gap-2"
                    v-if="canLogin"
                >
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('admin.dashboard')"
                        class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Painel
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200"
                        >
                            Login
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Criar conta
                        </Link>
                    </template>
                </div>
            </div>
        </nav>
    </header>
</template>
