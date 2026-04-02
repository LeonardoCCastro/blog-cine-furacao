<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    metrics: {
        type: Object,
        required: true,
    },
    featuredPosts: {
        type: Array,
        required: true,
    },
    topByViews: {
        type: Array,
        required: true,
    },
    activitySeries: {
        type: Array,
        required: true,
    },
    topCategories: {
        type: Array,
        required: true,
    },
});

const chartHeight = 280;
const chartWidth = 720;

const maxMetricValue = computed(() => {
    const maxViews = Math.max(...props.activitySeries.map((item) => item.views), 0);
    const maxComments = Math.max(...props.activitySeries.map((item) => item.comments_count), 0);
    return Math.max(maxViews, maxComments, 1);
});

const chartPoints = computed(() => {
    if (!props.activitySeries.length) {
        return { views: '', comments: '' };
    }

    const stepX = props.activitySeries.length > 1 ? chartWidth / (props.activitySeries.length - 1) : 0;

    const mapPoint = (value, index) => {
        const x = stepX * index;
        const y = chartHeight - (value / maxMetricValue.value) * (chartHeight - 30) - 10;
        return `${x},${Math.max(14, y)}`;
    };

    return {
        views: props.activitySeries.map((item, index) => mapPoint(item.views, index)).join(' '),
        comments: props.activitySeries.map((item, index) => mapPoint(item.comments_count, index)).join(' '),
    };
});

const chartArea = computed(() => {
    if (!chartPoints.value.views) {
        return '';
    }

    return `0,${chartHeight} ${chartPoints.value.views} ${chartWidth},${chartHeight}`;
});

const publicationBreakdown = computed(() => {
    const published = Number(props.metrics.published_posts || 0);
    const drafts = Number(props.metrics.draft_posts || 0);
    const total = Math.max(published + drafts, 1);

    return {
        published,
        drafts,
        publishedPercent: (published / total) * 100,
        draftPercent: (drafts / total) * 100,
    };
});

const publicationChartStyle = computed(() => {
    const published = publicationBreakdown.value.publishedPercent;
    return {
        background: `conic-gradient(#2563eb 0 ${published}%, #f59e0b ${published}% 100%)`,
    };
});

const quickStats = computed(() => [
    {
        label: 'Media de views por post',
        value: props.metrics.average_views_per_post,
        tone: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
    },
    {
        label: 'Media de comentarios por post',
        value: props.metrics.average_comments_per_post,
        tone: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
    },
    {
        label: 'Taxa de publicacao',
        value: `${props.metrics.publication_rate}%`,
        tone: 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300',
    },
    {
        label: 'Categorias e tags',
        value: `${props.metrics.categories_count} / ${props.metrics.tags_count}`,
        tone: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
    },
]);

function formatDate(date) {
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'short',
    });
}
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Visao geral editorial e de desempenho do blog.</p>
            </div>
        </template>

        <section class="space-y-6">
            <div
                class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6"
            >
                <div class="border-b border-gray-200 pb-5 dark:border-gray-700">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600 dark:text-blue-400"
                            >
                                Painel principal
                            </p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-gray-900 dark:text-white">
                                Saude do blog e da operacao editorial
                            </h2>
                            <p class="mt-2 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                Baseado no layout de customer service, mas focado no que importa aqui: posts, alcance,
                                comentarios e producao.
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300"
                        >
                            Ultimos {{ activitySeries.length }} posts usados no grafico de atividade
                        </div>
                    </div>
                </div>

                <div
                    class="grid gap-5 border-b border-gray-200 py-5 dark:border-gray-700 xl:grid-cols-[minmax(0,1fr)_280px]"
                >
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <article class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-900">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Posts totais</p>
                            <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white">
                                {{ metrics.total_posts }}
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Base completa de conteudo gerenciado
                            </p>
                        </article>

                        <article class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-900">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Posts publicados</p>
                            <p class="mt-3 text-3xl font-black text-emerald-600">{{ metrics.published_posts }}</p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Conteudos ativos no site publico
                            </p>
                        </article>

                        <article class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-900">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Visualizacoes acumuladas</p>
                            <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white">
                                {{ metrics.total_views }}
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Soma de alcance das postagens</p>
                        </article>

                        <article class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-900">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Comentarios</p>
                            <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white">
                                {{ metrics.total_comments }}
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Interacoes registradas nas postagens
                            </p>
                        </article>
                    </div>

                    <div
                        class="rounded-3xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-900"
                    >
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-gray-500 dark:text-gray-400">
                            Publicacao
                        </p>

                        <div class="mt-5 flex items-center gap-5">
                            <div
                                class="relative h-28 w-28 rounded-full"
                                :style="publicationChartStyle"
                            >
                                <div class="absolute inset-[14px] rounded-full bg-white dark:bg-gray-900" />
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-black text-gray-900 dark:text-white"
                                        >{{ metrics.publication_rate }}%</span
                                    >
                                    <span
                                        class="text-[11px] uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400"
                                        >Publicados</span
                                    >
                                </div>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="h-3 w-3 rounded-full bg-blue-600" />
                                    <span class="text-gray-600 dark:text-gray-300"
                                        >Publicados: {{ publicationBreakdown.published }}</span
                                    >
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="h-3 w-3 rounded-full bg-amber-500" />
                                    <span class="text-gray-600 dark:text-gray-300"
                                        >Rascunhos: {{ publicationBreakdown.drafts }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Atividade recente de posts
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Views e comentarios dos ultimos posts criados.
                            </p>
                        </div>

                        <Link
                            :href="route('admin.posts.index')"
                            class="inline-flex items-center text-sm font-medium text-blue-600 hover:underline"
                        >
                            Ir para gerenciamento de posts
                        </Link>
                    </div>

                    <div
                        class="mt-5 overflow-hidden rounded-3xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div
                            v-if="activitySeries.length"
                            class="space-y-4"
                        >
                            <svg
                                :viewBox="`0 0 ${chartWidth} ${chartHeight}`"
                                class="h-72 w-full"
                            >
                                <defs>
                                    <linearGradient
                                        id="viewsAreaGradient"
                                        x1="0"
                                        x2="0"
                                        y1="0"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#2563eb"
                                            stop-opacity="0.35"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="#2563eb"
                                            stop-opacity="0.03"
                                        />
                                    </linearGradient>
                                </defs>

                                <line
                                    v-for="index in 5"
                                    :key="`grid-${index}`"
                                    x1="0"
                                    :y1="(chartHeight / 5) * index"
                                    :x2="chartWidth"
                                    :y2="(chartHeight / 5) * index"
                                    stroke="rgba(148, 163, 184, 0.22)"
                                    stroke-dasharray="5 8"
                                />

                                <polygon
                                    :points="chartArea"
                                    fill="url(#viewsAreaGradient)"
                                />
                                <polyline
                                    :points="chartPoints.views"
                                    fill="none"
                                    stroke="#2563eb"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <polyline
                                    :points="chartPoints.comments"
                                    fill="none"
                                    stroke="#10b981"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />

                                <circle
                                    v-for="(item, index) in activitySeries"
                                    :key="`views-${item.id}`"
                                    :cx="
                                        activitySeries.length > 1
                                            ? (chartWidth / (activitySeries.length - 1)) * index
                                            : chartWidth / 2
                                    "
                                    :cy="chartHeight - (item.views / maxMetricValue) * (chartHeight - 30) - 10"
                                    r="5"
                                    fill="#2563eb"
                                />
                                <circle
                                    v-for="(item, index) in activitySeries"
                                    :key="`comments-${item.id}`"
                                    :cx="
                                        activitySeries.length > 1
                                            ? (chartWidth / (activitySeries.length - 1)) * index
                                            : chartWidth / 2
                                    "
                                    :cy="chartHeight - (item.comments_count / maxMetricValue) * (chartHeight - 30) - 10"
                                    r="5"
                                    fill="#10b981"
                                />
                            </svg>

                            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-7">
                                <div
                                    v-for="item in activitySeries"
                                    :key="item.id"
                                    class="rounded-2xl bg-white p-3 shadow-sm dark:bg-gray-800"
                                >
                                    <p
                                        class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400"
                                    >
                                        {{ formatDate(item.created_at) }}
                                    </p>
                                    <p class="mt-2 truncate text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ item.short_title }}
                                    </p>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ item.views }} views / {{ item.comments_count }} comentarios
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p
                            v-else
                            class="text-sm text-gray-500 dark:text-gray-400"
                        >
                            Ainda nao ha dados suficientes para exibir o grafico.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
                <article
                    v-for="stat in quickStats"
                    :key="stat.label"
                    class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-5"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl"
                        :class="stat.tone"
                    >
                        <span class="text-lg font-black">{{ String(stat.value).slice(0, 2) }}</span>
                    </div>
                    <div>
                        <p class="text-xl font-black text-gray-900 dark:text-white">{{ stat.value }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ stat.label }}</p>
                    </div>
                </article>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div
                    class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-700">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Posts recentes</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Ultimas postagens adicionadas ao painel.
                            </p>
                        </div>
                        <Link
                            :href="route('admin.posts.index')"
                            class="text-sm font-medium text-blue-600 hover:underline"
                            >Ver todos</Link
                        >
                    </div>

                    <ul class="mt-4 space-y-3">
                        <li
                            v-for="post in featuredPosts"
                            :key="post.id"
                            class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-900"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <Link
                                        :href="route('admin.posts.edit', post.id)"
                                        class="truncate font-semibold text-gray-900 hover:underline dark:text-white"
                                    >
                                        {{ post.title }}
                                    </Link>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ new Date(post.created_at).toLocaleDateString('pt-BR') }} /
                                        {{ post.views }} views / {{ post.comments_count }} comentarios
                                    </p>
                                    <Link
                                        v-if="post.published"
                                        :href="route('posts.show', post.slug)"
                                        class="mt-2 inline-flex text-xs font-medium text-blue-600 hover:underline"
                                    >
                                        Abrir publicamente
                                    </Link>
                                </div>

                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="
                                        post.published
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                    "
                                >
                                    {{ post.published ? 'Publicado' : 'Rascunho' }}
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="space-y-6">
                    <div
                        class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="border-b border-gray-200 pb-4 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Top por visualizacoes</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Conteudos com melhor alcance acumulado.
                            </p>
                        </div>

                        <div class="mt-4 space-y-4">
                            <article
                                v-for="(post, index) in topByViews"
                                :key="post.id"
                                class="flex items-center gap-4"
                            >
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-sm font-black text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                >
                                    {{ index + 1 }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <Link
                                        :href="route('admin.posts.edit', post.id)"
                                        class="truncate font-semibold text-gray-900 hover:underline dark:text-white"
                                    >
                                        {{ post.title }}
                                    </Link>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ post.views }} views / {{ post.comments_count }} comentarios
                                    </p>
                                </div>
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="
                                        post.published
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                    "
                                >
                                    {{ post.published ? 'Publicado' : 'Rascunho' }}
                                </span>
                            </article>
                        </div>
                    </div>

                    <div
                        class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="border-b border-gray-200 pb-4 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Categorias mais usadas</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Distribuicao por editoria principal.</p>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div
                                v-for="category in topCategories"
                                :key="category.id"
                            >
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-700 dark:text-gray-200">{{
                                        category.name
                                    }}</span>
                                    <span class="text-gray-500 dark:text-gray-400"
                                        >{{ category.posts_count }} post(s)</span
                                    >
                                </div>
                                <div class="h-2.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-900">
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-blue-600 to-cyan-400"
                                        :style="{
                                            width: `${Math.max((category.posts_count / Math.max(...topCategories.map((item) => item.posts_count), 1)) * 100, 8)}%`,
                                        }"
                                    />
                                </div>
                            </div>

                            <p
                                v-if="topCategories.length === 0"
                                class="text-sm text-gray-500 dark:text-gray-400"
                            >
                                Nenhuma categoria cadastrada ainda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
