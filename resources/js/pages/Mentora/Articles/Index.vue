<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface ArticleItem {
    id: number;
    title: string;
    slug: string;
    category: string;
    excerpt: string | null;
    published_at: string | null;
}

interface PaginatedArticles {
    items: ArticleItem[];
    total: number;
    current_page: number;
    last_page: number;
    per_page: number;
}

const props = defineProps<{
    articles: PaginatedArticles;
    filters: { search: string; category: string };
}>();

const keyword = ref(props.filters.search ?? '');
const category = ref(props.filters.category ?? '');
const hasFilters = computed(() => Boolean(props.filters.search || props.filters.category));

function paramsFor(page = 1): Record<string, string | number> {
    const params: Record<string, string | number> = {};
    if (keyword.value.trim()) params.search = keyword.value.trim();
    if (category.value.trim()) params.category = category.value.trim();
    if (page > 1) params.page = page;
    return params;
}

function submitSearch(): void {
    router.get('/artikel', paramsFor(), { preserveState: false, replace: true });
}

function clearFilters(): void {
    keyword.value = '';
    category.value = '';
    router.get('/artikel', {}, { preserveState: false, replace: true });
}

function goToPage(page: number): void {
    if (page < 1 || page > props.articles.last_page) return;
    router.get('/artikel', paramsFor(page), {
        preserveState: false,
        preserveScroll: true,
    });
}

function formatDate(value: string | null): string {
    if (!value) return 'Tanggal belum diinformasikan';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return 'Tanggal belum diinformasikan';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta',
    }).format(date);
}
</script>

<template>
    <PublicLayout>
        <Head title="Artikel & Panduan Beasiswa - MENTORA" />
        <!-- Fondasi fungsional. Desain katalog final pada S1-04.5E. -->
        <section class="bg-mentora-canvas py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-sm font-semibold uppercase tracking-wide text-mentora-primary">MENTORA INSIGHTS</p>
                <h1 class="mt-3 text-3xl font-bold text-mentora-navy md:text-4xl">Artikel dan Panduan Beasiswa</h1>
                <p class="mt-3 max-w-2xl leading-7 text-slate-600">
                    Temukan artikel edukasi, informasi pendidikan, dan panduan untuk mempersiapkan peluang beasiswa.
                </p>

                <form class="mt-8 grid gap-4 rounded-2xl border border-mentora-border bg-white p-5 shadow-sm md:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_auto] md:items-end" @submit.prevent="submitSearch">
                    <div class="min-w-0">
                        <label for="article-search" class="mb-2 block text-sm font-semibold text-mentora-navy">Cari artikel</label>
                        <input id="article-search" v-model="keyword" maxlength="150" type="search" placeholder="Judul atau ringkasan artikel..." class="min-h-12 w-full rounded-xl border border-mentora-border px-4 py-3 text-sm text-mentora-navy outline-none focus:border-mentora-primary" />
                    </div>
                    <div class="min-w-0">
                        <label for="article-category" class="mb-2 block text-sm font-semibold text-mentora-navy">Kategori</label>
                        <input id="article-category" v-model="category" maxlength="60" type="text" placeholder="Semua kategori" class="min-h-12 w-full rounded-xl border border-mentora-border px-4 py-3 text-sm text-mentora-navy outline-none focus:border-mentora-primary" />
                    </div>
                    <button type="submit" class="min-h-12 rounded-xl bg-mentora-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-mentora-primary-hover">Cari Artikel</button>
                </form>

                <div class="mt-9 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-bold text-mentora-navy">Jelajahi Artikel</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ articles.total }} artikel dipublikasikan sesuai pencarian</p>
                    </div>
                    <button v-if="hasFilters" type="button" class="rounded-xl border border-mentora-border bg-white px-4 py-2.5 text-sm font-semibold text-mentora-primary hover:border-mentora-primary" @click="clearFilters">Hapus Filter</button>
                </div>

                <div v-if="articles.items.length === 0" class="mt-6 rounded-2xl border border-mentora-border bg-white px-6 py-14 text-center shadow-sm">
                    <h3 class="text-xl font-bold text-mentora-navy">{{ hasFilters ? 'Artikel Tidak Ditemukan' : 'Belum Ada Artikel yang Ditampilkan' }}</h3>
                    <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-slate-600">
                        {{ hasFilters ? 'Coba kata kunci atau kategori lainnya.' : 'Artikel yang telah dipublikasikan akan muncul di sini.' }}
                    </p>
                    <button v-if="hasFilters" type="button" class="mt-5 rounded-xl bg-mentora-primary px-5 py-3 text-sm font-semibold text-white" @click="clearFilters">Lihat Semua Artikel</button>
                </div>

                <div v-else class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <article v-for="item in articles.items" :key="item.id" class="flex flex-col rounded-2xl border border-mentora-border bg-white p-6 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-mentora-primary">{{ item.category }}</p>
                        <h3 class="mt-3 text-lg font-bold leading-snug text-mentora-navy">{{ item.title }}</h3>
                        <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">{{ item.excerpt || 'Baca artikel selengkapnya untuk mendapatkan informasi lebih lanjut.' }}</p>
                        <p class="mt-5 text-xs text-slate-500">{{ formatDate(item.published_at) }}</p>
                        <Link :href="`/artikel/${encodeURIComponent(item.slug)}`" class="mt-4 inline-flex w-fit rounded-xl bg-mentora-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-mentora-primary-hover">Baca Selengkapnya</Link>
                    </article>
                </div>

                <nav v-if="articles.last_page > 1" class="mt-9 flex items-center justify-center gap-4" aria-label="Navigasi halaman artikel">
                    <button type="button" :disabled="articles.current_page <= 1" class="rounded-xl border border-mentora-border bg-white px-4 py-2 text-sm text-mentora-navy disabled:cursor-not-allowed disabled:opacity-40" @click="goToPage(articles.current_page - 1)">Sebelumnya</button>
                    <span class="text-sm text-slate-600">{{ articles.current_page }} / {{ articles.last_page }}</span>
                    <button type="button" :disabled="articles.current_page >= articles.last_page" class="rounded-xl border border-mentora-border bg-white px-4 py-2 text-sm text-mentora-navy disabled:cursor-not-allowed disabled:opacity-40" @click="goToPage(articles.current_page + 1)">Berikutnya</button>
                </nav>
            </div>
        </section>
    </PublicLayout>
</template>
