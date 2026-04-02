<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    posts: Array,
});

function deletePost(id) {
    if (confirm('Tem certeza que deseja deletar este post?')) {
        router.delete(route('posts.destroy', id));
    }
}
</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900 py-3 sm:py-5">
        <div class="px-4 mx-auto max-w-screen-2xl lg:px-12">
            <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                <div class="px-4 divide-y dark:divide-gray-700">
                    <div
                        class="flex flex-col items-stretch justify-between py-4 space-y-3 md:flex-row md:items-center md:space-y-0"
                    >
                        <div class="flex items-center flex-1 space-x-4">
                            <h5>
                                <span class="text-gray-500">Posts cadastrados: </span>
                                <span class="dark:text-white">{{ posts.length }}</span>
                            </h5>
                        </div>
                        <Link
                            :href="href"
                            class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                        >
                            <svg
                                class="w-5 h-5 mr-2"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 12h14m-7 7V5"
                                />
                            </svg>
                            Novo Post
                        </Link>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-4 py-3"
                                >
                                    Título
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3"
                                >
                                    Categoria
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3"
                                >
                                    Resumo
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3"
                                >
                                    Autor
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3"
                                >
                                    Ativo
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 whitespace-nowrap"
                                >
                                    Publicado em:
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 whitespace-nowrap"
                                >
                                    Última atualização:
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3"
                                >
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="post in posts"
                                :key="post.id"
                                class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <th
                                    scope="row"
                                    class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    <div class="flex items-center">
                                        {{ post.title }}
                                    </div>
                                </th>
                                <td class="px-4 py-2">
                                    <div
                                        class="inline-flex items-center bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-3.5 w-3.5 mr-1"
                                            viewbox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                            />
                                        </svg>
                                        {{ post.category }}
                                    </div>
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex items-center">
                                        {{ post.excerpt }}
                                    </div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img
                                            src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/avatar-10.png"
                                            alt="iMac Front Image"
                                            class="w-auto h-8 mr-3 rounded-full"
                                        />
                                        {{ post.user }}
                                    </div>
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            value=""
                                            class="sr-only peer"
                                            name="promote"
                                        />
                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"
                                        ></div>
                                    </label>
                                </td>
                                <td class="px-4 py-2">{{ post.created_at }}</td>
                                <td class="px-4 py-2">{{ post.updated_at }}</td>
                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <button
                                        :id="`dropdown-button-${post.id}`"
                                        type="button"
                                        :data-dropdown-toggle="`dropdown-${post.id}`"
                                        class="inline-flex items-center p-1 text-sm font-medium text-center text-gray-500 rounded-lg hover:text-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            aria-hidden="true"
                                            fill="currentColor"
                                            viewbox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                                            />
                                        </svg>
                                    </button>
                                    <div
                                        :id="`dropdown-${post.id}`"
                                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded shadow w-44 dark:bg-gray-700 dark:divide-gray-600"
                                    >
                                        <ul
                                            class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                            :aria-labelledby="`dropdown-button-${post.id}`"
                                        >
                                            <li>
                                                <a
                                                    :href="`/posts/${post.slug}`"
                                                    target="_blank"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                    >Show</a
                                                >
                                            </li>
                                            <li>
                                                <Link
                                                    :href="route('posts.create') + '?edit=' + post.id"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                    >Editar</Link
                                                >
                                            </li>
                                        </ul>
                                        <div class="py-1">
                                            <button
                                                @click="deletePost(post.id)"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav
                    class="flex flex-col items-start justify-between p-4 space-y-3 md:flex-row md:items-center md:space-y-0"
                    aria-label="Table navigation"
                >
                    <div class="flex items-center space-x-3">
                        <label
                            for="rows"
                            class="text-xs font-normal text-gray-500 dark:text-gray-400"
                            >Rows per page</label
                        ><select
                            id="rows"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block py-1.5 pl-3.5 pr-6 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option
                                selected=""
                                value="10"
                            >
                                10
                            </option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <div class="text-xs font-normal text-gray-500 dark:text-gray-400">
                            <span class="font-semibold text-gray-900 dark:text-white">1-10</span>
                            of
                            <span class="font-semibold text-gray-900 dark:text-white">100</span>
                        </div>
                    </div>
                    <ul class="inline-flex items-stretch -space-x-px">
                        <li>
                            <a
                                href="#"
                                class="flex text-sm w-20 items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                >Previous</a
                            >
                        </li>
                        <li>
                            <a
                                href="#"
                                class="flex text-sm w-20 items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                >Next</a
                            >
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
</template>
