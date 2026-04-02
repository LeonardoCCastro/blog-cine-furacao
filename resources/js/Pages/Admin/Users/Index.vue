<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    userMetrics: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const editingId = ref(null);
const search = ref('');
const roleFilter = ref('all');

const createForm = useForm({
    name: '',
    email: '',
    role: 'writer',
    password: '',
    password_confirmation: '',
});

const editForm = useForm({
    name: '',
    email: '',
    role: 'writer',
    password: '',
    password_confirmation: '',
});

const filteredUsers = computed(() =>
    props.users.filter((user) => {
        const matchesSearch = `${user.name} ${user.email}`.toLowerCase().includes(search.value.trim().toLowerCase());
        const matchesRole = roleFilter.value === 'all' || user.role === roleFilter.value;

        return matchesSearch && matchesRole;
    }),
);

const summaryCards = computed(() => [
    {
        label: 'Usuarios do painel',
        value: props.userMetrics.total,
        tone: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
    },
    {
        label: 'Administradores',
        value: props.userMetrics.admins,
        tone: 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300',
    },
    {
        label: 'Escritores',
        value: props.userMetrics.writers,
        tone: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
    },
    {
        label: 'Posts vinculados',
        value: props.userMetrics.posts_total,
        tone: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
    },
]);

function submitCreate() {
    createForm.post(route('admin.users.store'), {
        onSuccess: () => createForm.reset(),
    });
}

function startEdit(user) {
    editingId.value = user.id;
    editForm.reset();
    editForm.clearErrors();
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.role = user.role;
}

function cancelEdit() {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
}

function submitEdit(userId) {
    editForm.put(route('admin.users.update', userId), {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
}

function removeUser(user) {
    if (!window.confirm(`Deseja remover este ${user.role === 'admin' ? 'administrador' : 'escritor'}?`)) {
        return;
    }

    router.delete(route('admin.users.destroy', user.id), {
        preserveScroll: true,
    });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('pt-BR');
}
</script>

<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Usuarios</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Gerencie administradores e escritores com o novo layout de lista.
                </p>
            </div>
        </template>

        <section class="space-y-6">
            <div
                v-if="page.props.flash.success"
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300"
            >
                {{ page.props.flash.success }}
            </div>
            <div
                v-if="page.props.flash.error"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900/40 dark:bg-gray-900/20 dark:text-red-300"
            >
                {{ page.props.flash.error }}
            </div>

            <div
                class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6"
            >
                <div
                    class="flex flex-col gap-4 border-b border-gray-200 pb-5 dark:border-gray-700 sm:flex-row sm:items-start sm:justify-between"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600 dark:text-blue-400">
                            Equipe
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-gray-900 dark:text-white">
                            Lista de usuarios do painel
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                            Adaptei o layout do `list.html` para o seu caso de uso, focando em cadastro, filtros e
                            manutencao de permissoes.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300"
                    >
                        {{ filteredUsers.length }} usuario(s) visivel(is)
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article
                        v-for="card in summaryCards"
                        :key="card.label"
                        class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl font-black"
                            :class="card.tone"
                        >
                            {{ String(card.value).slice(0, 2) }}
                        </div>
                        <div>
                            <p class="text-xl font-black text-gray-900 dark:text-white">{{ card.value }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ card.label }}</p>
                        </div>
                    </article>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-5 py-5 dark:border-gray-700">
                    <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_420px]">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cadastrar novo usuario</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Crie administradores ou escritores sem sair da listagem.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="relative sm:col-span-2">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-width="2"
                                            d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                                        />
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Buscar por nome ou email"
                                    class="block w-full rounded-2xl border border-gray-300 bg-gray-50 p-3 pl-10 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-900/30"
                                />
                            </div>

                            <select
                                v-model="roleFilter"
                                class="block w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-900/30"
                            >
                                <option value="all">Todos os perfis</option>
                                <option value="admin">Administradores</option>
                                <option value="writer">Escritores</option>
                            </select>

                            <div
                                class="rounded-2xl border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400"
                            >
                                Filtro local ativo na tabela abaixo
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-b border-gray-200 px-5 py-5 dark:border-gray-700">
                    <form
                        class="grid gap-4 md:grid-cols-2 xl:grid-cols-5"
                        @submit.prevent="submitCreate"
                    >
                        <div class="xl:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                            <input
                                v-model="createForm.name"
                                type="text"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            />
                            <p
                                v-if="createForm.errors.name"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ createForm.errors.name }}
                            </p>
                        </div>

                        <div class="xl:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input
                                v-model="createForm.email"
                                type="email"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            />
                            <p
                                v-if="createForm.errors.email"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ createForm.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Permissao</label
                            >
                            <select
                                v-model="createForm.role"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            >
                                <option value="writer">Escritor</option>
                                <option value="admin">Administrador</option>
                            </select>
                            <p
                                v-if="createForm.errors.role"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ createForm.errors.role }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
                            <input
                                v-model="createForm.password"
                                type="password"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            />
                            <p
                                v-if="createForm.errors.password"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ createForm.errors.password }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Confirmar senha</label
                            >
                            <input
                                v-model="createForm.password_confirmation"
                                type="password"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            />
                        </div>

                        <div class="md:col-span-2 xl:col-span-3 flex items-end">
                            <button
                                type="submit"
                                :disabled="createForm.processing"
                                class="inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-70"
                            >
                                {{ createForm.processing ? 'Criando...' : 'Criar usuario' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead
                            class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-900/40 dark:text-gray-400"
                        >
                            <tr>
                                <th class="px-5 py-4 font-semibold">Usuario</th>
                                <th class="px-5 py-4 font-semibold">Permissao</th>
                                <th class="px-5 py-4 font-semibold">Email</th>
                                <th class="px-5 py-4 font-semibold">Posts</th>
                                <th class="px-5 py-4 font-semibold">Criado em</th>
                                <th class="px-5 py-4 text-right font-semibold">Acoes</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr
                                v-for="user in filteredUsers"
                                :key="user.id"
                                class="hover:bg-gray-50/70 dark:hover:bg-gray-900/20"
                            >
                                <template v-if="editingId === user.id">
                                    <td class="px-5 py-4 align-top">
                                        <div class="space-y-3">
                                            <input
                                                v-model="editForm.name"
                                                type="text"
                                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                            />
                                            <input
                                                v-model="editForm.email"
                                                type="email"
                                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                            />
                                            <p
                                                v-if="editForm.errors.name"
                                                class="text-sm text-red-600"
                                            >
                                                {{ editForm.errors.name }}
                                            </p>
                                            <p
                                                v-if="editForm.errors.email"
                                                class="text-sm text-red-600"
                                            >
                                                {{ editForm.errors.email }}
                                            </p>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <select
                                            v-model="editForm.role"
                                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                        >
                                            <option value="writer">Escritor</option>
                                            <option value="admin">Administrador</option>
                                        </select>
                                        <p
                                            v-if="editForm.errors.role"
                                            class="mt-2 text-sm text-red-600"
                                        >
                                            {{ editForm.errors.role }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4 align-top text-gray-700 dark:text-gray-300">
                                        {{ user.email }}
                                    </td>

                                    <td class="px-5 py-4 align-top text-gray-700 dark:text-gray-300">
                                        {{ user.posts_count }}
                                    </td>

                                    <td class="px-5 py-4 align-top">
                                        <p class="text-gray-700 dark:text-gray-300">
                                            {{ formatDate(user.created_at) }}
                                        </p>
                                        <div class="mt-3 space-y-2">
                                            <input
                                                v-model="editForm.password"
                                                type="password"
                                                placeholder="Nova senha (opcional)"
                                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                            />
                                            <input
                                                v-model="editForm.password_confirmation"
                                                type="password"
                                                placeholder="Confirmar nova senha"
                                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                            />
                                            <p
                                                v-if="editForm.errors.password"
                                                class="text-sm text-red-600"
                                            >
                                                {{ editForm.errors.password }}
                                            </p>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-right align-top">
                                        <div class="inline-flex items-center gap-3">
                                            <button
                                                type="button"
                                                class="text-sm font-medium text-blue-600 hover:underline"
                                                @click="submitEdit(user.id)"
                                            >
                                                Salvar
                                            </button>
                                            <button
                                                type="button"
                                                class="text-sm font-medium text-gray-600 hover:underline dark:text-gray-300"
                                                @click="cancelEdit"
                                            >
                                                Cancelar
                                            </button>
                                        </div>
                                    </td>
                                </template>

                                <template v-else>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-900 text-sm font-black text-white dark:bg-white dark:text-gray-900"
                                            >
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-gray-900 dark:text-white">
                                                    {{ user.name }}
                                                </p>
                                                <p
                                                    v-if="user.is_current_user"
                                                    class="text-xs font-medium text-blue-600 dark:text-blue-400"
                                                >
                                                    Sua conta atual
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="
                                                user.role === 'admin'
                                                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
                                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                            "
                                        >
                                            {{ user.role === 'admin' ? 'Administrador' : 'Escritor' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ user.posts_count }}
                                    </td>
                                    <td class="px-5 py-4 text-gray-700 dark:text-gray-300">
                                        {{ formatDate(user.created_at) }}
                                    </td>

                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-3">
                                            <button
                                                type="button"
                                                class="text-sm font-medium text-blue-600 hover:underline"
                                                @click="startEdit(user)"
                                            >
                                                Editar
                                            </button>
                                            <button
                                                type="button"
                                                class="text-sm font-medium text-red-600 hover:underline"
                                                @click="removeUser(user)"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </td>
                                </template>
                            </tr>

                            <tr v-if="filteredUsers.length === 0">
                                <td
                                    colspan="6"
                                    class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Nenhum usuario encontrado para os filtros atuais.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
