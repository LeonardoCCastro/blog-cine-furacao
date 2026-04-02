<script setup>
import { computed, ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const sidebarOpen = ref(false);
const notifications = computed(() => page.props.adminNotifications ?? { unread_count: 0, items: [] });
const isAdmin = computed(() => Boolean(page.props.auth.user?.is_admin));
const menuItems = computed(() => [
    { label: 'Dashboard', routeName: 'admin.dashboard', active: () => route().current('admin.dashboard') },
    { label: 'Posts', routeName: 'admin.posts.index', active: () => route().current('admin.posts.*') },
    ...(isAdmin.value
        ? [
              {
                  label: 'Categorias',
                  routeName: 'admin.categories.index',
                  active: () => route().current('admin.categories.*'),
              },
              {
                  label: 'Tags',
                  routeName: 'admin.subcategories.index',
                  active: () => route().current('admin.subcategories.*'),
              },
              { label: 'Usuarios', routeName: 'admin.users.index', active: () => route().current('admin.users.*') },
          ]
        : []),
]);
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="lg:hidden border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <Link
                    :href="route('posts.index')"
                    class="text-lg font-bold text-gray-900 dark:text-white"
                    >Cine Furacao</Link
                >
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="rounded-lg border border-gray-300 p-2 text-gray-700 dark:border-gray-600 dark:text-gray-200"
                >
                    <span class="sr-only">Menu</span>
                    <svg
                        class="h-5 w-5"
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

        <div class="flex">
            <aside
                class="fixed inset-y-0 left-0 z-30 w-72 transform border-r border-gray-200 bg-white p-4 transition-transform dark:border-gray-700 dark:bg-gray-900 lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="mb-6 hidden items-center gap-2 px-2 pt-2 lg:flex">
                    <Link
                        :href="route('posts.index')"
                        class="text-xl font-bold text-gray-900 dark:text-white"
                        >Cine Furacao</Link
                    >
                </div>

                <nav class="space-y-1">
                    <Link
                        v-for="item in menuItems"
                        :key="item.routeName"
                        :href="route(item.routeName)"
                        class="block rounded-lg px-3 py-2 text-sm font-medium"
                        :class="
                            item.active()
                                ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300'
                                : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800'
                        "
                        @click="sidebarOpen = false"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div
                    class="mt-8 rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800"
                >
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Atalho</p>
                    <div class="mt-2 space-y-2">
                        <Link
                            :href="route('admin.posts.create')"
                            class="block rounded-lg bg-blue-600 px-3 py-2 text-center text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Novo post
                        </Link>
                        <Link
                            :href="route('posts.index')"
                            class="block rounded-lg border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            Ver site publico
                        </Link>
                    </div>
                </div>
            </aside>

            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-20 bg-black/40 lg:hidden"
                @click="sidebarOpen = false"
            />

            <div class="min-h-screen w-full lg:pl-72">
                <header
                    class="border-b border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-900 sm:px-6 lg:px-8"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <slot name="header" />
                        </div>

                        <div class="flex items-center gap-2">
                            <div
                                class="relative"
                                v-if="$page.props.auth.user?.is_admin"
                            >
                                <Dropdown
                                    align="right"
                                    width="96"
                                >
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                                        >
                                            <span class="sr-only">Notificacoes</span>
                                            <svg
                                                class="h-5 w-5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"
                                                />
                                            </svg>
                                            <span
                                                v-if="notifications.unread_count > 0"
                                                class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-xs font-semibold text-white"
                                            >
                                                {{ notifications.unread_count > 9 ? '9+' : notifications.unread_count }}
                                            </span>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="p-2">
                                            <div class="mb-1 flex items-center justify-between px-2 py-1">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Comentarios recentes
                                                </p>
                                                <Link
                                                    :href="route('admin.notifications.read-all')"
                                                    method="post"
                                                    as="button"
                                                    class="text-xs font-medium text-blue-600 hover:underline"
                                                >
                                                    Marcar todas
                                                </Link>
                                            </div>
                                            <div
                                                v-if="notifications.items.length === 0"
                                                class="rounded-lg px-2 py-3 text-sm text-gray-500"
                                            >
                                                Nenhuma notificacao.
                                            </div>
                                            <div
                                                v-else
                                                class="max-h-96 space-y-1 overflow-y-auto"
                                            >
                                                <Link
                                                    v-for="notification in notifications.items"
                                                    :key="notification.id"
                                                    :href="route('admin.notifications.open', notification.id)"
                                                    class="block rounded-lg px-2 py-2"
                                                    :class="
                                                        notification.read_at
                                                            ? 'hover:bg-gray-100 dark:hover:bg-gray-800'
                                                            : 'bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30'
                                                    "
                                                >
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                                        Novo comentario em &quot;{{
                                                            notification.data.post_title
                                                        }}&quot;
                                                    </p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300">
                                                        {{ notification.data.comment_author }}:
                                                        {{ notification.data.comment_preview }}
                                                    </p>
                                                </Link>
                                            </div>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <div class="relative">
                                <Dropdown
                                    align="right"
                                    width="48"
                                >
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                                        >
                                            {{ $page.props.auth.user.name }}
                                            <svg
                                                class="h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">Perfil</DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            >Sair</DropdownLink
                                        >
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
