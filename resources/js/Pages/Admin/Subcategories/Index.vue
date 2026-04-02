<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    categories: {
        type: Array,
        required: true,
    },
    subcategories: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const editingId = ref(null);

const createForm = useForm({
    name: '',
    parent_id: '',
});

const editForm = useForm({
    name: '',
});

function submitCreate() {
    createForm.post(route('admin.subcategories.store'), {
        onSuccess: () => createForm.reset(),
    });
}

function startEdit(subcategory) {
    editingId.value = subcategory.id;
    editForm.reset();
    editForm.clearErrors();
    editForm.name = subcategory.name;
}

function cancelEdit() {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
}

function submitEdit(subcategoryId) {
    editForm.put(route('admin.subcategories.update', subcategoryId), {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
}

function destroySubcategory(subcategoryId) {
    if (!window.confirm('Deseja remover esta tag?')) {
        return;
    }

    router.delete(route('admin.subcategories.destroy', subcategoryId), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Tags" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Tags</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Gerencie subcategorias vinculadas as categorias principais.
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

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Nova tag</h2>

                <form
                    class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2"
                    @submit.prevent="submitCreate"
                >
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                        <select
                            v-model="createForm.parent_id"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        >
                            <option
                                disabled
                                value=""
                            >
                                Selecione uma categoria
                            </option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                        <p
                            v-if="createForm.errors.parent_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ createForm.errors.parent_id }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Nome da tag</label
                        >
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

                    <div class="md:col-span-2">
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
                    v-for="subcategory in subcategories"
                    :key="subcategory.id"
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-start justify-between gap-3">
                        <template v-if="editingId === subcategory.id">
                            <div class="w-full">
                                <p
                                    class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"
                                >
                                    {{ subcategory.parent?.name || 'Sem categoria' }}
                                </p>
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
                                        @click="submitEdit(subcategory.id)"
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
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"
                                >
                                    {{ subcategory.parent?.name || 'Sem categoria' }}
                                </p>
                                <h3 class="mt-1 font-semibold text-gray-900 dark:text-white">{{ subcategory.name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ subcategory.posts_count }} post(s)
                                </p>
                            </div>
                            <div class="inline-flex items-center gap-3">
                                <button
                                    type="button"
                                    class="text-sm font-medium text-blue-600 hover:underline"
                                    @click="startEdit(subcategory)"
                                >
                                    Editar
                                </button>
                                <button
                                    type="button"
                                    class="text-sm font-medium text-red-600 hover:underline"
                                    @click="destroySubcategory(subcategory.id)"
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
