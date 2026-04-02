<script setup>
defineProps({
    canLogin: {
        type: Boolean,
        default: false,
    },
    canRegister: {
        type: Boolean,
        default: false,
    },
    laravelVersion: {
        type: String,
        default: '',
    },
    phpVersion: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <footer class="mt-16 bg-[linear-gradient(180deg,#151515_0%,#0d0d0d_100%)] text-white">
        <div class="mx-auto max-w-7xl px-4 py-14">
            <div class="grid gap-10 border-b border-white/10 pb-10 lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-red-500">Cine Furacao</p>
                    <h2 class="mt-3 text-3xl font-black uppercase tracking-tight">
                        Noticias, reviews e destaques do dia.
                    </h2>
                    <p class="mt-4 max-w-md text-sm leading-6 text-white/70">
                        Um rodape mais editorial, com atalhos rapidos para as principais editorias e acesso direto ao
                        arquivo do site.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link
                            :href="route('posts.all', { category: 'noticias' })"
                            class="border border-white/15 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white/80 transition hover:border-red-500 hover:text-red-400"
                        >
                            Noticias
                        </Link>
                        <Link
                            :href="route('posts.all', { category: 'review' })"
                            class="border border-white/15 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white/80 transition hover:border-red-500 hover:text-red-400"
                        >
                            Review
                        </Link>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-white/60">Editorias</p>
                    <div class="mt-4 space-y-3 text-sm text-white/80">
                        <Link
                            :href="route('posts.index')"
                            class="block hover:text-red-400"
                            >Home</Link
                        >
                        <Link
                            :href="route('posts.all', { category: 'noticias' })"
                            class="block hover:text-red-400"
                            >Noticias</Link
                        >
                        <Link
                            :href="route('posts.all', { category: 'review' })"
                            class="block hover:text-red-400"
                            >Review</Link
                        >
                        <Link
                            :href="route('posts.all')"
                            class="block hover:text-red-400"
                            >Arquivo completo</Link
                        >
                    </div>
                </div>

                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-white/60">Acesso</p>
                    <div class="mt-4 space-y-3 text-sm text-white/80">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('admin.dashboard')"
                            class="block hover:text-red-400"
                            >Dashboard</Link
                        >
                        <Link
                            v-else-if="canLogin"
                            :href="route('login')"
                            class="block hover:text-red-400"
                            >Login</Link
                        >
                        <Link
                            v-if="!$page.props.auth.user && canRegister"
                            :href="route('register')"
                            class="block hover:text-red-400"
                            >Criar conta</Link
                        >
                        <Link
                            :href="route('profile.edit')"
                            v-if="$page.props.auth.user"
                            class="block hover:text-red-400"
                            >Perfil</Link
                        >
                    </div>
                </div>

                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-white/60">Base</p>
                    <div class="mt-4 space-y-3 text-sm text-white/80">
                        <p>Laravel v{{ laravelVersion }}</p>
                        <p>PHP v{{ phpVersion }}</p>
                        <p>Layout editorial customizado</p>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col gap-4 pt-6 text-xs uppercase tracking-[0.2em] text-white/45 sm:flex-row sm:items-center sm:justify-between"
            >
                <p>Cine Furacao</p>
                <p>Noticias, review e arquivo editorial</p>
            </div>
        </div>
    </footer>
</template>
