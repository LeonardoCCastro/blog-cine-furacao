<script setup>
import axios from 'axios';
import Quill from 'quill';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import 'quill/dist/quill.snow.css';

const props = defineProps({
    post: {
        type: Object,
        required: false,
        default: null,
    },
    submitUrl: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        required: true,
    },
    buttonLabel: {
        type: String,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: props.post?.title ?? '',
    category_id: props.post?.category_id ?? '',
    subcategory_id: props.post?.subcategory_id ?? '',
    new_subcategory_name: '',
    excerpt: props.post?.excerpt ?? '',
    content: props.post?.content ?? '',
    published: Boolean(props.post?.published ?? false),
    image: null,
});

const editorElement = ref(null);
const imageInput = ref(null);
const isUploadingEditorMedia = ref(false);
const previewUrl = ref(props.post?.image_url ?? '');
const submissionErrorMessage = ref('');
const pendingUploads = new Set();
const processedDataImageSources = new Set();
let quill = null;
let generatedPreviewUrl = null;

const selectedCategory = computed(
    () => props.categories.find((category) => category.id === Number(form.category_id)) ?? null,
);
const availableSubcategories = computed(() => selectedCategory.value?.subcategories ?? []);
const selectedSubcategory = computed(
    () => availableSubcategories.value.find((subcategory) => subcategory.id === Number(form.subcategory_id)) ?? null,
);
const statusLabel = computed(() => (form.published ? 'Publicado' : 'Rascunho'));
const pageTitle = computed(() => (props.method === 'post' ? 'Novo post' : 'Editar post'));
const pageDescription = computed(() =>
    props.method === 'post'
        ? 'Monte a estrutura da postagem e publique quando estiver pronta.'
        : 'Atualize o conteudo, a taxonomia e a imagem destacada da postagem.',
);
const tagPreview = computed(() => {
    const tags = [];

    if (selectedSubcategory.value) {
        tags.push(selectedSubcategory.value.name);
    }

    const newTagName = form.new_subcategory_name.trim();
    if (newTagName !== '') {
        tags.push(newTagName);
    }

    return tags;
});

const toolbarOptions = [
    [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
    [{ header: [1, 2, 3, 4, 5, 6, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ color: [] }, { background: [] }],
    [{ script: 'sub' }, { script: 'super' }],
    [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
    [{ indent: '-1' }, { indent: '+1' }],
    [{ direction: 'rtl' }],
    [{ align: [] }],
    ['blockquote', 'code-block'],
    ['link', 'image', 'video'],
    ['clean'],
];

watch(
    () => form.category_id,
    () => {
        const stillValid = availableSubcategories.value.some(
            (subcategory) => subcategory.id === Number(form.subcategory_id),
        );
        if (!stillValid) {
            form.subcategory_id = '';
        }
    },
);

watch(
    () => form.image,
    (file) => {
        if (generatedPreviewUrl) {
            URL.revokeObjectURL(generatedPreviewUrl);
            generatedPreviewUrl = null;
        }

        if (!file) {
            previewUrl.value = props.post?.image_url ?? '';
            return;
        }

        generatedPreviewUrl = URL.createObjectURL(file);
        previewUrl.value = generatedPreviewUrl;
    },
);

function trackUpload(promise) {
    pendingUploads.add(promise);
    isUploadingEditorMedia.value = true;

    promise.finally(() => {
        pendingUploads.delete(promise);
        isUploadingEditorMedia.value = pendingUploads.size > 0;
    });

    return promise;
}

async function uploadEditorImage(file) {
    const formData = new FormData();
    formData.append('image', file);

    const response = await trackUpload(
        axios.post(route('admin.posts.editor-media.image.store'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),
    );

    return response?.data?.url ?? null;
}

function dataUrlToFile(dataUrl, fileName = 'pasted-image.png') {
    const matches = dataUrl.match(/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.+)$/);
    if (!matches) return null;

    const mimeType = matches[1];
    const base64Data = matches[2];
    const binary = atob(base64Data);
    const bytes = new Uint8Array(binary.length);

    for (let i = 0; i < binary.length; i += 1) {
        bytes[i] = binary.charCodeAt(i);
    }

    return new File([bytes], fileName, { type: mimeType });
}

function syncFormContent() {
    if (!quill) return;

    const html = quill.root.innerHTML
        .replace(/https?:\/\/localhost(?::\d+)?\/storage\//gi, '/storage/')
        .replace(/https?:\/\/127\.0\.0\.1(?::\d+)?\/storage\//gi, '/storage/');

    form.content = html === '<p><br></p>' ? '' : html;
}

async function insertUploadedImage(file) {
    if (!file || !quill) return;

    try {
        const imageUrl = await uploadEditorImage(file);
        if (!imageUrl) return;

        const range = quill.getSelection(true);
        const index = range?.index ?? quill.getLength();
        quill.insertEmbed(index, 'image', imageUrl, 'user');
        quill.setSelection(index + 1, 0, 'silent');
        syncFormContent();
    } catch {
        window.alert('Nao foi possivel enviar a imagem. Tente novamente.');
    }
}

function normalizeVideoUrl(url) {
    if (!url) return '';

    const trimmedUrl = url.trim();

    if (trimmedUrl.includes('youtube.com/watch?v=')) {
        const videoId = trimmedUrl.split('v=')[1]?.split('&')[0];
        return videoId ? `https://www.youtube.com/embed/${videoId}` : trimmedUrl;
    }

    if (trimmedUrl.includes('youtu.be/')) {
        const videoId = trimmedUrl.split('youtu.be/')[1]?.split('?')[0];
        return videoId ? `https://www.youtube.com/embed/${videoId}` : trimmedUrl;
    }

    return trimmedUrl;
}

function insertImageFromFile() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';

    input.onchange = async () => {
        const file = input.files?.[0];
        await insertUploadedImage(file);
    };

    input.click();
}

function insertVideoFromUrl() {
    if (!quill) return;

    const inputUrl = window.prompt('Cole a URL do video');
    if (!inputUrl) return;

    const videoUrl = normalizeVideoUrl(inputUrl);
    if (!/^https?:\/\//i.test(videoUrl)) return;

    const range = quill.getSelection(true);
    const index = range?.index ?? quill.getLength();

    quill.insertEmbed(index, 'video', videoUrl, 'user');
    quill.setSelection(index + 1, 0, 'silent');
}

async function handleClipboardImages(event) {
    if (!quill) return;

    const clipboardItems = Array.from(event.clipboardData?.items ?? []);
    const imageFiles = clipboardItems
        .filter((item) => item.type?.startsWith('image/'))
        .map((item) => item.getAsFile())
        .filter(Boolean);

    if (!imageFiles.length) return;

    event.preventDefault();
    event.stopImmediatePropagation();

    const selection = quill.getSelection(true);
    if (selection?.length) {
        quill.deleteText(selection.index, selection.length, 'user');
        quill.setSelection(selection.index, 0, 'silent');
    }

    for (const imageFile of imageFiles) {
        // eslint-disable-next-line no-await-in-loop
        await insertUploadedImage(imageFile);
    }
}

async function handleDropImages(event) {
    if (!quill) return;

    const imageFiles = Array.from(event.dataTransfer?.files ?? []).filter((file) => file.type?.startsWith('image/'));
    if (!imageFiles.length) return;

    event.preventDefault();
    event.stopImmediatePropagation();
    quill.focus();

    for (const imageFile of imageFiles) {
        // eslint-disable-next-line no-await-in-loop
        await insertUploadedImage(imageFile);
    }
}

function handleDragOver(event) {
    event.preventDefault();
}

async function replaceInlineDataImages() {
    if (!quill) return;

    const dataImages = quill.root.querySelectorAll('img[src^="data:image/"]');
    for (const imageElement of dataImages) {
        const currentSource = imageElement.getAttribute('src');
        if (!currentSource || processedDataImageSources.has(currentSource)) continue;

        processedDataImageSources.add(currentSource);
        const imageFile = dataUrlToFile(currentSource);
        if (!imageFile) continue;

        try {
            // eslint-disable-next-line no-await-in-loop
            const uploadedUrl = await uploadEditorImage(imageFile);
            if (!uploadedUrl) continue;
            imageElement.setAttribute('src', uploadedUrl);
            syncFormContent();
        } catch {
            processedDataImageSources.delete(currentSource);
        }
    }
}

function handleImageSelection(event) {
    form.image = event.target.files?.[0] ?? null;
}

function clearSelectedImage() {
    form.image = null;

    if (imageInput.value) {
        imageInput.value.value = '';
    }
}

onMounted(() => {
    quill = new Quill(editorElement.value, {
        theme: 'snow',
        modules: {
            toolbar: {
                container: toolbarOptions,
                handlers: {
                    image: insertImageFromFile,
                    video: insertVideoFromUrl,
                },
            },
        },
    });

    if (form.content) {
        quill.clipboard.dangerouslyPasteHTML(form.content);
        replaceInlineDataImages();
    }

    quill.root.addEventListener('paste', handleClipboardImages, true);
    quill.root.addEventListener('drop', handleDropImages, true);
    quill.root.addEventListener('dragover', handleDragOver);

    quill.on('text-change', async () => {
        syncFormContent();
        await replaceInlineDataImages();
    });
});

onBeforeUnmount(() => {
    if (generatedPreviewUrl) {
        URL.revokeObjectURL(generatedPreviewUrl);
    }

    if (quill?.root) {
        quill.root.removeEventListener('paste', handleClipboardImages, true);
        quill.root.removeEventListener('drop', handleDropImages, true);
        quill.root.removeEventListener('dragover', handleDragOver);
    }

    quill = null;
});

async function submit() {
    submissionErrorMessage.value = '';

    if (pendingUploads.size) {
        await Promise.allSettled(Array.from(pendingUploads));
    }

    syncFormContent();

    if (/src="data:image\//i.test(form.content)) {
        window.alert('Ainda existem imagens pendentes no editor. Aguarde o upload e tente novamente.');
        return;
    }

    const normalizedData = {
        ...form.data(),
        category_id: form.category_id ? Number(form.category_id) : null,
        subcategory_id: form.subcategory_id ? Number(form.subcategory_id) : null,
        new_subcategory_name: form.new_subcategory_name.trim(),
        published: form.published ? 1 : 0,
    };

    if (props.method === 'post') {
        form.transform(() => normalizedData).post(props.submitUrl, {
            forceFormData: true,
            preserveScroll: true,
            onError: () => {
                submissionErrorMessage.value = 'Revise os campos destacados antes de salvar a postagem.';
            },
        });
        return;
    }

    form.transform(() => ({ ...normalizedData, _method: 'put' })).post(props.submitUrl, {
        forceFormData: true,
        preserveScroll: true,
        onError: () => {
            submissionErrorMessage.value = 'Revise os campos destacados antes de salvar a postagem.';
        },
    });
}
</script>

<template>
    <form
        @submit.prevent="submit"
        class="space-y-6"
    >
        <div
            v-if="submissionErrorMessage"
            class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300"
        >
            {{ submissionErrorMessage }}
        </div>

        <section
            class="overflow-hidden rounded-[28px] border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
        >
            <div
                class="border-b border-gray-200 bg-gradient-to-r from-gray-50 via-white to-gray-50 px-6 py-5 dark:border-gray-700 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
            >
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600 dark:text-blue-400">
                            Editor
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-gray-900 dark:text-white">
                            {{ pageTitle }}
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ pageDescription }}</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <Link
                            :href="route('admin.posts.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            Voltar para posts
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing || isUploadingEditorMedia"
                            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ form.processing ? 'Salvando...' : buttonLabel }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-6 xl:grid-cols-[minmax(0,1fr)_340px]">
                <div class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <label
                            for="post-title"
                            class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white"
                            >Titulo da postagem</label
                        >
                        <input
                            id="post-title"
                            v-model="form.title"
                            type="text"
                            placeholder="Digite um titulo forte para chamar atencao"
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-900/30"
                        />
                        <p
                            v-if="form.errors.title"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div>
                                <h3
                                    class="text-sm font-semibold uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400"
                                >
                                    Conteudo
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    O editor abaixo continua usando Quill com upload de imagem e video.
                                </p>
                            </div>

                            <span
                                v-if="isUploadingEditorMedia"
                                class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                            >
                                Enviando imagens...
                            </span>
                        </div>

                        <div
                            class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900"
                        >
                            <div
                                ref="editorElement"
                                class="quill-editor bg-white dark:bg-gray-900"
                            />
                        </div>
                        <p
                            v-if="form.errors.content"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.content }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <label
                            for="post-excerpt"
                            class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white"
                            >Resumo</label
                        >
                        <textarea
                            id="post-excerpt"
                            v-model="form.excerpt"
                            rows="5"
                            placeholder="Escreva um resumo curto para listagens, cards e SEO on-page."
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-900/30"
                        />
                        <p
                            v-if="form.errors.excerpt"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.excerpt }}
                        </p>
                    </div>
                </div>

                <aside class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">
                            Publicacao
                        </h3>

                        <div class="mt-4 rounded-2xl bg-gray-50 p-4 dark:bg-gray-900">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ statusLabel }}</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{
                                            form.published
                                                ? 'O post sera exibido publicamente.'
                                                : 'O post sera salvo fora do ar.'
                                        }}
                                    </p>
                                </div>

                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input
                                        v-model="form.published"
                                        type="checkbox"
                                        class="peer sr-only"
                                    />
                                    <span
                                        class="h-6 w-11 rounded-full bg-gray-300 transition peer-checked:bg-blue-600 dark:bg-gray-600"
                                    />
                                    <span
                                        class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"
                                    />
                                </label>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing || isUploadingEditorMedia"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-black disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200"
                        >
                            {{ form.processing ? 'Salvando...' : buttonLabel }}
                        </button>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">
                            Categoria e tags
                        </h3>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Categoria principal</label
                                >
                                <select
                                    v-model="form.category_id"
                                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-900/30"
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
                                    v-if="form.errors.category_id"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.category_id }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Tag existente</label
                                >
                                <select
                                    v-model="form.subcategory_id"
                                    :disabled="!form.category_id"
                                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-900/30"
                                >
                                    <option value="">Sem tag</option>
                                    <option
                                        v-for="subcategory in availableSubcategories"
                                        :key="subcategory.id"
                                        :value="subcategory.id"
                                    >
                                        {{ subcategory.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.subcategory_id"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.subcategory_id }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Nova tag</label
                                >
                                <input
                                    v-model="form.new_subcategory_name"
                                    type="text"
                                    :disabled="!form.category_id"
                                    placeholder="Ex: Bastidores, Teorias, Analises"
                                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-900/30"
                                />
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Se preencher, a tag sera criada dentro da categoria selecionada e vinculada ao post.
                                </p>
                                <p
                                    v-if="form.errors.new_subcategory_name"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.new_subcategory_name }}
                                </p>
                            </div>

                            <div
                                v-if="selectedCategory || tagPreview.length"
                                class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-900"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400"
                                >
                                    Preview da estrutura
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span
                                        v-if="selectedCategory"
                                        class="inline-flex rounded-full bg-gray-900 px-3 py-1 text-xs font-semibold text-white dark:bg-white dark:text-gray-900"
                                    >
                                        {{ selectedCategory.name }}
                                    </span>
                                    <span
                                        v-for="tag in tagPreview"
                                        :key="tag"
                                        class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                    >
                                        {{ tag }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">
                            Imagem destacada
                        </h3>

                        <div class="mt-4 space-y-4">
                            <label
                                for="post-image"
                                class="flex min-h-52 cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center transition hover:border-blue-400 hover:bg-blue-50/40 dark:border-gray-600 dark:bg-gray-900 dark:hover:border-blue-500 dark:hover:bg-blue-900/10"
                            >
                                <input
                                    id="post-image"
                                    ref="imageInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleImageSelection"
                                />

                                <template v-if="previewUrl">
                                    <img
                                        :src="previewUrl"
                                        alt="Preview da imagem destacada"
                                        class="h-40 w-full rounded-2xl object-cover shadow-sm"
                                    />
                                    <p class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                                        Clique para trocar a imagem
                                    </p>
                                </template>

                                <template v-else>
                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-gray-500 shadow-sm dark:bg-gray-800 dark:text-gray-300"
                                    >
                                        <svg
                                            class="h-7 w-7"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.7"
                                                d="M12 16V4m0 0-4 4m4-4 4 4M4 16.5V18a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1.5"
                                            />
                                        </svg>
                                    </div>
                                    <p class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                                        Clique para enviar a imagem destacada
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        PNG, JPG ou WEBP. A imagem atual sera substituida ao salvar.
                                    </p>
                                </template>
                            </label>

                            <div
                                class="flex items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400"
                            >
                                <span>{{
                                    form.image?.name ||
                                    (previewUrl ? 'Imagem pronta para o post' : 'Nenhuma imagem selecionada')
                                }}</span>
                                <button
                                    v-if="form.image"
                                    type="button"
                                    class="font-semibold text-red-600 hover:underline"
                                    @click="clearSelectedImage"
                                >
                                    Descartar nova imagem
                                </button>
                            </div>

                            <p
                                v-if="form.errors.image"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.image }}
                            </p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    </form>
</template>

<style scoped>
:deep(.ql-toolbar.ql-snow) {
    border: 0;
    border-bottom: 1px solid rgb(229 231 235);
    padding: 0.85rem 1rem;
}

:deep(.ql-container.ql-snow) {
    min-height: 24rem;
    border: 0;
}

:deep(.ql-editor) {
    min-height: 24rem;
    font-size: 1rem;
    line-height: 1.75;
}

:deep(.ql-editor.ql-blank::before) {
    color: rgb(156 163 175);
    font-style: normal;
}

:deep(.dark .ql-toolbar.ql-snow) {
    border-bottom-color: rgb(55 65 81);
}
</style>
