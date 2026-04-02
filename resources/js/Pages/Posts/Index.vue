<script setup>
import ApplicationLayout from '@/Layouts/ApplicationLayout.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  weeklyTop: {
    type: Array,
    required: true,
  },
  latestPosts: {
    type: Array,
    required: true,
  },
  categorySections: {
    type: Array,
    required: true,
  },
  trendingPosts: {
    type: Array,
    required: true,
  },
});

const page = usePage();

const featuredStory = computed(() => props.weeklyTop[0] ?? null);
const heroSideStories = computed(() => props.weeklyTop.slice(1, 5));
const radarStories = computed(() => props.weeklyTop.slice(5, 7));
const homepageCategories = computed(() => (page.props.blogCategories ?? []).slice(0, 8));
const latestLead = computed(() => props.latestPosts[0] ?? null);
const latestFeed = computed(() => props.latestPosts.slice(1));
const spotlightSections = computed(() => props.categorySections.slice(0, 2));
const remainingSections = computed(() => props.categorySections.slice(2));

function formatDate(value) {
  return new Date(value).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}
</script>

<template>
  <ApplicationLayout title="Cine Furacao">
    <section
      class="border-b border-gray-200 bg-gradient-to-b from-gray-50 via-white to-white dark:border-gray-700 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
      <div class="mx-auto max-w-7xl px-4 py-8 sm:py-10">
        <div
          class="mb-8 flex flex-col gap-4 border-b border-gray-200 pb-5 dark:border-gray-700 lg:flex-row lg:items-end lg:justify-between">
          <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.34em] text-red-600">Radar semanal</p>
            <h1 class="mt-3 text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-5xl">
              O pulso de cinema, series e cultura pop com cara de home editorial
            </h1>
            <p class="mt-4 max-w-2xl text-sm leading-6 text-gray-600 dark:text-gray-300">
              Mantive a energia Gematsu da home atual, mas usei o `homepage.html` como base para dar mais
              ritmo, respiro e descoberta de conteudo.
            </p>
          </div>

          <div class="flex flex-wrap gap-3">
            <Link :href="route('posts.all')"
              class="inline-flex items-center rounded-full border border-gray-300 px-4 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-gray-700 transition hover:border-red-500 hover:text-red-600 dark:border-gray-600 dark:text-gray-200 dark:hover:border-red-500 dark:hover:text-red-400">
            Ver arquivo completo
            </Link>
          </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.95fr)]">
          <article v-if="featuredStory"
            class="group overflow-hidden rounded-[28px] border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <Link :href="route('posts.show', featuredStory.slug)" class="block">
            <div class="relative h-[320px] overflow-hidden bg-gray-950 sm:h-[420px]">
              <img :src="featuredStory.image_url" :alt="featuredStory.title"
                class="absolute inset-0 block h-full w-full object-cover object-center transition duration-700 scale-[1.03] group-hover:scale-110" />
              <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent" />
              <div class="absolute inset-x-0 bottom-0 p-4 sm:p-6">
                <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-300">
                  {{ featuredStory.category?.name || 'Sem categoria' }}
                  <span v-if="featuredStory.subcategory">
                    / {{ featuredStory.subcategory.name }}</span>
                </p>
                <h2 class="mt-2 max-w-3xl text-2xl font-black uppercase leading-tight text-white sm:text-4xl">
                  {{ featuredStory.title }}
                </h2>
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-300">
                  {{ formatDate(featuredStory.created_at) }}
                  / {{ featuredStory.views }} views
                </p>
              </div>
            </div>
            </Link>
          </article>

          <div class="grid gap-4 sm:auto-rows-[190px] sm:grid-cols-2">
            <article v-for="post in heroSideStories" :key="post.id"
              class="group h-[180px] overflow-hidden rounded-[24px] border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 dark:border-gray-700 dark:bg-gray-800 sm:h-[190px]">
              <Link :href="route('posts.show', post.slug)" class="block h-full">
              <div class="relative h-full w-full overflow-hidden bg-gray-950">
                <img :src="post.image_url" :alt="post.title"
                  class="absolute inset-0 block h-full w-full object-cover object-center transition duration-500 scale-[1.05] group-hover:scale-110" />
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/25 to-transparent" />
                <div class="absolute inset-x-0 bottom-0 p-3 sm:p-4">
                  <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-300">
                    {{ post.category?.name || 'Sem categoria' }}
                    <span v-if="post.subcategory"> / {{ post.subcategory.name }}</span>
                  </p>
                  <h3 class="mt-2 text-base font-black uppercase leading-tight text-white sm:text-lg">
                    {{ post.title }}
                  </h3>
                  <p class="mt-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-gray-300">
                    {{ formatDate(post.created_at) }}
                  </p>
                </div>
              </div>
              </Link>
            </article>
          </div>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
          <div class="flex flex-wrap gap-3">
            <Link :href="route('posts.all')"
              class="rounded-full border border-red-600 px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-red-600 transition hover:bg-red-50 dark:border-red-500 dark:text-red-400 dark:hover:bg-red-900/20">
            Todas as editorias
            </Link>
            <Link v-for="category in homepageCategories" :key="category.id"
              :href="route('posts.all', { category: category.slug })"
              class="rounded-full border border-transparent bg-gray-100 px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-gray-700 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            {{ category.name }}
            </Link>
          </div>

          <div v-if="radarStories.length"
            class="rounded-[24px] border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-red-600">Radar extra</p>
            <div class="mt-4 space-y-3">
              <article v-for="post in radarStories" :key="post.id"
                class="border-b border-gray-200 pb-3 last:border-0 last:pb-0 dark:border-gray-700">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-gray-400">
                  {{ post.category?.name || 'Cine Furacao' }}
                </p>
                <h3 class="mt-2 text-base font-black uppercase leading-tight text-gray-900 dark:text-white">
                  <Link :href="route('posts.show', post.slug)" class="hover:text-red-600 dark:hover:text-red-400">{{
                  post.title }}</Link>
                </h3>
              </article>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-10 px-4 py-10 lg:grid-cols-[minmax(0,1fr)_320px]">
      <div>
        <div class="flex items-end justify-between gap-4 border-b-2 border-gray-900 pb-3 dark:border-white">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-red-600">Ultimas noticias</p>
            <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
              Cobertura em destaque
            </h2>
          </div>
          <Link :href="route('posts.all')"
            class="text-sm font-semibold uppercase tracking-wide text-gray-600 hover:text-red-600 dark:text-gray-300">
          Todas as postagens
          </Link>
        </div>

        <article v-if="latestLead"
          class="mt-6 overflow-hidden rounded-[28px] border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div class="grid gap-0 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)]">
            <Link :href="route('posts.show', latestLead.slug)" class="relative min-h-[320px] overflow-hidden">
            <img :src="latestLead.image_url" :alt="latestLead.title"
              class="absolute inset-0 h-full w-full object-cover transition duration-700 hover:scale-105" />
            </Link>

            <div class="flex flex-col justify-between p-6 sm:p-8">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.26em] text-gray-500 dark:text-gray-400">
                  {{ formatDate(latestLead.created_at) }}
                  <span v-if="latestLead.category"> / {{ latestLead.category.name }}</span>
                  <span v-if="latestLead.subcategory"> / {{ latestLead.subcategory.name }}</span>
                </p>
                <h3 class="mt-3 text-3xl font-black uppercase leading-tight text-gray-900 dark:text-white">
                  <Link :href="route('posts.show', latestLead.slug)" class="hover:text-red-600 dark:hover:text-red-400">
                  {{ latestLead.title }}</Link>
                </h3>
                <p class="mt-4 text-sm leading-7 text-gray-600 dark:text-gray-300">
                  {{ latestLead.excerpt || 'Sem resumo cadastrado para esta postagem.' }}
                </p>
              </div>

              <div
                class="mt-6 flex flex-wrap items-center gap-4 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                <span>{{ latestLead.user?.name || 'Redacao Cine Furacao' }}</span>
                <span>{{ latestLead.views }} views</span>
                <Link :href="route('posts.show', latestLead.slug)"
                  class="text-red-600 hover:underline dark:text-red-400">
                Ler materia
                </Link>
              </div>
            </div>
          </div>
        </article>

        <div class="mt-8 divide-y divide-gray-200 dark:divide-gray-700">
          <article v-for="post in latestFeed" :key="post.id" class="grid gap-4 py-6 sm:grid-cols-[220px_minmax(0,1fr)]">
            <Link :href="route('posts.show', post.slug)" class="block overflow-hidden rounded-2xl bg-gray-200">
            <img :src="post.image_url" :alt="post.title"
              class="h-full min-h-[150px] w-full object-cover transition duration-500 hover:scale-105" />
            </Link>

            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
                {{ formatDate(post.created_at) }}
                <span v-if="post.category"> / {{ post.category.name }}</span>
                <span v-if="post.subcategory"> / {{ post.subcategory.name }}</span>
              </p>
              <h3 class="mt-2 text-2xl font-black uppercase leading-tight text-gray-900 dark:text-white">
                <Link :href="route('posts.show', post.slug)" class="hover:text-red-600 dark:hover:text-red-400">{{
                post.title }}</Link>
              </h3>
              <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                {{ post.excerpt || 'Sem resumo cadastrado para esta postagem.' }}
              </p>
              <p class="mt-4 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
                {{ post.user?.name || 'Redacao Cine Furacao' }}
              </p>
            </div>
          </article>
        </div>

        <section v-for="section in spotlightSections" :key="section.id" class="mt-12">
          <div class="mb-4 flex items-end justify-between gap-4 border-b border-gray-200 pb-3 dark:border-gray-700">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.3em] text-red-600">
                Editoria em foco
              </p>
              <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                {{ section.name }}
              </h2>
            </div>
            <Link :href="route('posts.all', { category: section.slug })"
              class="text-sm font-semibold uppercase tracking-wide text-gray-600 hover:text-red-600 dark:text-gray-300">
            Ver mais
            </Link>
          </div>

          <div class="grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.95fr)]">
            <article v-if="section.posts[0]"
              class="group overflow-hidden rounded-[28px] border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
              <Link :href="route('posts.show', section.posts[0].slug)" class="block">
              <div class="relative h-[320px] overflow-hidden bg-gray-950 sm:h-[420px]">
                <img :src="section.posts[0].image_url" :alt="section.posts[0].title"
                  class="absolute inset-0 block h-full w-full object-cover object-center transition duration-700 scale-[1.03] group-hover:scale-110" />
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent" />
                <div class="absolute inset-x-0 bottom-0 p-4 sm:p-6">
                  <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-gray-300">
                    {{ section.posts[0].category?.name || section.name }}
                    <span v-if="section.posts[0].subcategory"> / {{ section.posts[0].subcategory.name }}</span>
                  </p>
                  <h3 class="mt-2 max-w-3xl text-2xl font-black uppercase leading-tight text-white sm:text-4xl">
                    {{ section.posts[0].title }}
                  </h3>
                  <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-300">
                    {{ formatDate(section.posts[0].created_at) }}
                    / {{ section.posts[0].views }} views
                  </p>
                </div>
              </div>
              </Link>
            </article>

            <div class="grid gap-4 sm:auto-rows-[200px] sm:grid-cols-2">
              <article v-for="post in section.posts.slice(1, 5)" :key="post.id"
                class="group h-[180px] overflow-hidden rounded-[24px] border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 dark:border-gray-700 dark:bg-gray-800 sm:h-[200px]">
                <Link :href="route('posts.show', post.slug)" class="block h-full">
                <div class="relative h-full w-full overflow-hidden bg-gray-950">
                  <img :src="post.image_url" :alt="post.title"
                    class="absolute inset-0 block h-full w-full object-cover object-center transition duration-500 scale-[1.05] group-hover:scale-110" />
                  <div class="absolute inset-0 bg-gradient-to-t from-black via-black/25 to-transparent" />
                  <div class="absolute inset-x-0 bottom-0 p-3 sm:p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-300">
                      {{ post.category?.name || section.name }}
                      <span v-if="post.subcategory"> / {{ post.subcategory.name }}</span>
                    </p>
                    <h4 class="mt-2 text-base font-black uppercase leading-tight text-white sm:text-lg">
                      {{ post.title }}
                    </h4>
                    <p class="mt-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-gray-300">
                      {{ formatDate(post.created_at) }}
                    </p>
                  </div>
                </div>
                </Link>
              </article>
            </div>
          </div>
        </section>

        <section v-if="remainingSections.length" class="mt-12">
          <div class="mb-4 border-b border-gray-200 pb-3 dark:border-gray-700">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-red-600">Mais editorias</p>
            <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
              Continue explorando
            </h2>
          </div>

          <div class="grid gap-6 md:grid-cols-2">
            <section v-for="section in remainingSections" :key="section.id"
              class="rounded-[24px] border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
              <div class="flex items-end justify-between gap-3 border-b border-gray-200 pb-3 dark:border-gray-700">
                <h3 class="text-xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                  {{ section.name }}
                </h3>
                <Link :href="route('posts.all', { category: section.slug })"
                  class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                Ver tudo
                </Link>
              </div>

              <div class="mt-4 space-y-4">
                <article v-for="post in section.posts" :key="post.id"
                  class="border-b border-gray-200 pb-4 last:border-0 last:pb-0 dark:border-gray-700">
                  <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gray-400">
                    {{ formatDate(post.created_at) }}
                  </p>
                  <h4 class="mt-2 text-lg font-black uppercase leading-tight text-gray-900 dark:text-white">
                    <Link :href="route('posts.show', post.slug)" class="hover:text-red-600 dark:hover:text-red-400">{{
                    post.title }}</Link>
                  </h4>
                </article>
              </div>
            </section>
          </div>
        </section>
      </div>

      <aside>
        <div class="space-y-6 lg:sticky lg:top-24">
          <div
            class="rounded-[28px] border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b-2 border-gray-900 pb-3 dark:border-white">
              <p class="text-xs font-semibold uppercase tracking-[0.3em] text-red-600">Trending</p>
              <h2 class="mt-2 text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white">
                Mais vistas do dia
              </h2>
            </div>

            <div class="mt-4 space-y-4">
              <article v-for="(post, index) in trendingPosts" :key="post.id"
                class="border-b border-gray-200 pb-4 last:border-0 last:pb-0 dark:border-gray-700">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">
                  0{{ index + 1 }}
                </p>
                <h3 class="mt-2 text-lg font-black uppercase leading-tight text-gray-900 dark:text-white">
                  <Link :href="route('posts.show', post.slug)" class="hover:text-red-600 dark:hover:text-red-400">{{
                  post.title }}</Link>
                </h3>
                <p class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                  {{ post.category?.name || 'Sem categoria' }} / {{ post.views }} views
                </p>
              </article>
            </div>
          </div>

          <div
            class="overflow-hidden rounded-[28px] border border-gray-200 bg-gradient-to-br from-red-600 via-red-700 to-gray-900 p-6 text-white shadow-sm dark:border-gray-700">
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-red-100">Boletim</p>
            <h2 class="mt-3 text-3xl font-black uppercase leading-tight">
              Receba o melhor da semana do Cine Furacao
            </h2>
            <p class="mt-3 text-sm leading-6 text-red-50/90">
              Ainda sem newsletter automatizada, mas este bloco entra como base visual inspirada no
              `homepage.html` e ja aponta o tom certo para futuras captacoes.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <Link :href="route('posts.all')"
                class="inline-flex rounded-full bg-white px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-gray-900 transition hover:bg-gray-100">
              Explorar arquivo
              </Link>
              <Link :href="route('posts.all', { category: 'review' })"
                class="inline-flex rounded-full border border-white/40 px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-white/10">
              Ler reviews
              </Link>
            </div>
          </div>
        </div>
      </aside>
    </section>
  </ApplicationLayout>
</template>
