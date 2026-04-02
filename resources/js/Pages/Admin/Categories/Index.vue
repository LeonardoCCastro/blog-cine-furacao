<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const editingId = ref(null);

const createForm = useForm({
    name: '',
});

const editForm = useForm({
    name: '',
});

function submitCreate() {
    createForm.post(route('admin.categories.store'), {
        onSuccess: () => createForm.reset(),
    });
}

function startEdit(category) {
    editingId.value = category.id;
    editForm.reset();
    editForm.clearErrors();
    editForm.name = category.name;
}

function cancelEdit() {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
}

function submitEdit(categoryId) {
    editForm.put(route('admin.categories.update', categoryId), {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
}

function destroyCategory(categoryId) {
    if (!window.confirm('Deseja remover esta categoria?')) {
        return;
    }

    router.delete(route('admin.categories.destroy', categoryId), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Categorias" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Categorias</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Organize o blog com categorias principais.</p>
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

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Nova categoria</h2>

                <form
                    class="mt-4 grid grid-cols-1 gap-4"
                    @submit.prevent="submitCreate"
                >
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                        <input
                            v-model="createForm.name"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        />
                        <p
                            v-if="createForm.errors.name"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ createForm.errors.name }}
                        </p>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-70"
                        >
                            Salvar
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <article
                    v-for="category in categories"
                    :key="category.id"
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-start justify-between gap-3">
                        <template v-if="editingId === category.id">
                            <div class="w-full">
                                <input
                                    v-model="editForm.name"
                                    type="text"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                />
                                <p
                                    v-if="editForm.errors.name"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ editForm.errors.name }}
                                </p>
                                <div class="mt-3 inline-flex items-center gap-3">
                                    <button
                                        type="button"
                                        class="text-sm font-medium text-blue-600 hover:underline"
                                        @click="submitEdit(category.id)"
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
                            </div>
                        </template>
                        <template v-else>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ category.name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ category.posts_count }} post(s)
                                </p>
                            </div>
                            <div class="inline-flex items-center gap-3">
                                <button
                                    type="button"
                                    class="text-sm font-medium text-blue-600 hover:underline"
                                    @click="startEdit(category)"
                                >
                                    Editar
                                </button>
                                <button
                                    type="button"
                                    class="text-sm font-medium text-red-600 hover:underline"
                                    @click="destroyCategory(category.id)"
                                >
                                    Excluir
                                </button>
                            </div>
                        </template>
                    </div>
                </article>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
