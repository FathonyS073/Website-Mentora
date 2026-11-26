<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowRight,
    ArrowUpRight,
    BookMarked,
    BookOpen,
    CalendarDays,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    ClipboardList,
    GraduationCap,
    HeartHandshake,
    Lightbulb,
    Megaphone,
    Search,
    Sparkles,
    X,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
import ArticleCard from '@/components/mentora/ArticleCard.vue';

interface ArticleItem {
    id: number;
    title: string;
    slug: string;
    category: string;
    excerpt: string | null;
    published_at: string | null;
    cover_image_path?: string | null;
}

interface PaginatedArticles {
    items: ArticleItem[];
    total: number;
    current_page: number;
    last_page: number;
    per_page: number;
}

const props = withDefaults(defineProps<{
    articles: PaginatedArticles;
    filters: { search: string; category: string };
    preview?: boolean;
}>(), { preview: false });

const categories = [
    { value: '', label: 'Semua Artikel', icon: BookOpen },
    { value: 'Tips Beasiswa', label: 'Tips Beasiswa', icon: GraduationCap },
    { value: 'Panduan Pendaftaran', label: 'Panduan Pendaftaran', icon: ClipboardList },
    { value: 'Persiapan Seleksi', label: 'Persiapan Seleksi', icon: BookMarked },
    { value: 'Pengembangan Diri', label: 'Pengembangan Diri', icon: Lightbulb },
    { value: 'Info & Update', label: 'Info & Update', icon: Megaphone },
    { value: 'Kisah Inspiratif', label: 'Kisah Inspiratif', icon: HeartHandshake },
];

const keyword = ref(props.filters.search ?? '');
const category = ref(props.filters.category ?? '');
const appliedPreviewSearch = ref(props.filters.search ?? '');

const activeSearch = computed(() => props.preview ? appliedPreviewSearch.value : props.filters.search);
const activeCategory = computed(() => props.preview ? category.value : props.filters.category);
const hasFilters = computed(() => Boolean(activeSearch.value || activeCategory.value));

const visibleArticles = computed(() => {
    if (!props.preview) return props.articles.items;
    const query = appliedPreviewSearch.value.trim().toLocaleLowerCase('id-ID');
    return props.articles.items.filter((article) => {
        const matchCategory = !category.value || article.category === category.value;
        const matchQuery = !query || `${article.title} ${article.excerpt ?? ''}`.toLocaleLowerCase('id-ID').includes(query);
        return matchCategory && matchQuery;
    });
});
const totalArticles = computed(() => props.preview ? visibleArticles.value.length : props.articles.total);
const featuredArticle = computed(() => visibleArticles.value[0] ?? null);
const sidebarArticles = computed(() => visibleArticles.value.slice(1, 4));
const remainingArticles = computed(() => visibleArticles.value.slice(4));

function makeParams(page = 1): Record<string, string | number> {
    const params: Record<string, string | number> = {};
    const search = keyword.value.trim();
    if (search) params.search = search;
    if (category.value.trim()) params.category = category.value.trim();
    if (page > 1) params.page = page;
    return params;
}

function submitSearch(): void {
    if (props.preview) {
        appliedPreviewSearch.value = keyword.value.trim();
        return;
    }
    router.get('/artikel', makeParams(), { preserveState: false, replace: true });
}

function changeCategory(value: string): void {
    category.value = value;
    if (!props.preview) {
        router.get('/artikel', makeParams(), { preserveState: false, replace: true });
    }
}

function clearFilters(): void {
    keyword.value = '';
    category.value = '';
    appliedPreviewSearch.value = '';
    if (!props.preview) router.get('/artikel', {}, { preserveState: false, replace: true });
}

function changePage(page: number): void {
    if (props.preview || page < 1 || page > props.articles.last_page) return;
    router.get('/artikel', makeParams(page), {
        preserveState: false,
        preserveScroll: true,
    });
}

function formatDate(value: string | null): string {
    if (!value) return 'Tanggal belum tersedia';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return 'Tanggal belum tersedia';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta',
    }).format(date);
}

function safeCoverPath(path?: string | null): string | null {
    if (!path) return null;
    const value = path.trim();
    if (value.startsWith('https://')) {
        try {
            const url = new URL(value);
            return url.protocol === 'https:' ? url.toString() : null;
        } catch {
            return null;
        }
    }
    if (value.startsWith('//') || value.includes('..') || value.includes('?') || value.includes('#')) return null;
    if (value.startsWith('/storage/') && /^\/storage\/[a-zA-Z0-9/_\-.]+$/.test(value)) return value;
    if (/^[a-zA-Z0-9][a-zA-Z0-9/_\-.]*$/.test(value)) return `/storage/${value}`;
    return null;
}
const featuredCover = computed(() => safeCoverPath(featuredArticle.value?.cover_image_path));
</script>

<template>
    <PublicLayout>
        <Head title="Artikel dan Panduan Beasiswa - MENTORA" />

        <main class="min-h-screen bg-mentora-canvas">
            <section class="relative overflow-hidden border-b border-indigo-100 bg-gradient-to-br from-indigo-50 via-[#f7f9ff] to-violet-100/60">
                <div class="pointer-events-none absolute -right-24 -top-44 h-[28rem] w-[28rem] rounded-full bg-indigo-200/35 blur-3xl" />
                <div class="pointer-events-none absolute -left-28 top-20 h-72 w-72 rounded-full bg-blue-100/70 blur-3xl" />

                <div class="relative mx-auto max-w-7xl px-4 pb-10 pt-10 sm:px-6 lg:px-8 lg:pb-14 lg:pt-14">
                    <div v-if="preview" class="mb-8 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-medium text-amber-900" role="note">
                        <strong>PREVIEW LOKAL</strong> — Seluruh artikel di halaman ini adalah data contoh antarmuka, bukan artikel yang dipublikasikan MENTORA.
                    </div>
                    <div class="grid items-center gap-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,.85fr)]">
                        <div class="min-w-0">
                            <span class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white/85 px-3 py-1.5 text-xs font-bold text-mentora-primary shadow-sm">
                                <BookOpen :size="16" aria-hidden="true" />
                                MENTORA INSIGHTS
                            </span>
                            <h1 class="mt-5 max-w-2xl text-3xl font-extrabold leading-tight tracking-tight text-mentora-navy sm:text-4xl lg:text-5xl">
                                Wawasan, Tips, dan Panduan <span class="text-mentora-primary">Seputar Beasiswa</span>
                            </h1>
                            <p class="mt-4 max-w-xl text-sm leading-7 text-slate-600 sm:text-base">
                                Temukan panduan praktis, strategi persiapan, serta informasi pendidikan untuk mendukung perjalanan studimu.
                            </p>

                            <form class="mt-7 flex flex-col gap-3 rounded-2xl border border-indigo-100 bg-white p-2 shadow-lg shadow-indigo-200/20 sm:flex-row sm:items-center" role="search" @submit.prevent="submitSearch">
                                <div class="flex min-w-0 flex-1 items-center gap-3 px-3">
                                    <Search :size="20" class="shrink-0 text-mentora-primary" aria-hidden="true" />
                                    <input
                                        v-model="keyword"
                                        type="search"
                                        maxlength="150"
                                        aria-label="Cari artikel berdasarkan judul atau ringkasan"
                                        placeholder="Cari tips, esai, wawancara, beasiswa..."
                                        class="min-h-11 w-full min-w-0 bg-transparent text-sm text-mentora-navy outline-none placeholder:text-slate-400"
                                    />
                                    <button v-if="keyword" type="button" aria-label="Hapus kata kunci" class="rounded-lg p-1 text-slate-400 hover:text-mentora-navy" @click="keyword = ''">
                                        <X :size="17" aria-hidden="true" />
                                    </button>
                                </div>
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-mentora-primary px-7 py-3 text-sm font-bold text-white transition hover:bg-mentora-primary-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary">
                                    Cari Artikel <ArrowRight :size="16" aria-hidden="true" />
                                </button>
                            </form>
                        </div>

                        <!-- Hero ilustratif tanpa gambar eksternal atau klaim publikasi palsu. -->
                        <div class="relative hidden min-h-[280px] items-center justify-center lg:flex" aria-hidden="true">
                            <div class="absolute h-[260px] w-[260px] rounded-[38%_62%_55%_45%] bg-indigo-200/70 rotate-12" />
                            <div class="absolute h-[220px] w-[220px] rounded-full bg-violet-200/50 blur-xl" />
                            <div class="relative flex h-48 w-48 rotate-[-6deg] items-center justify-center rounded-[2rem] border-2 border-white bg-gradient-to-br from-white to-indigo-100 shadow-2xl shadow-indigo-200/60">
                                <BookOpen :size="104" :stroke-width="1.1" class="text-mentora-primary" />
                                <Sparkles :size="30" class="absolute -right-2 -top-3 text-amber-500" />
                            </div>
                            <div class="absolute right-0 top-5 rounded-2xl border border-indigo-100 bg-white/95 px-5 py-4 shadow-lg">
                                <div class="flex items-center gap-2 text-xs font-semibold text-mentora-navy"><CheckCircle2 :size="17" class="text-mentora-primary" /> Panduan lengkap</div>
                                <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-mentora-navy"><CheckCircle2 :size="17" class="text-mentora-primary" /> Tips dan strategi</div>
                                <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-mentora-navy"><CheckCircle2 :size="17" class="text-mentora-primary" /> Info pendidikan</div>
                            </div>
                            <div class="absolute bottom-1 left-2 flex items-center gap-3 rounded-2xl border border-indigo-100 bg-white/95 px-4 py-3 shadow-lg">
                                <GraduationCap :size="27" class="text-mentora-primary" />
                                <div><p class="text-xs font-bold text-mentora-navy">Belajar & berkembang</p><p class="text-[11px] text-slate-500">Bersama MENTORA</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="mx-auto max-w-7xl px-4 pb-16 pt-7 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2 overflow-x-auto rounded-2xl border border-mentora-border bg-white p-2 shadow-sm" aria-label="Kategori artikel">
                    <button
                        v-for="item in categories"
                        :key="item.label"
                        type="button"
                        :aria-pressed="activeCategory === item.value"
                        class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-xs font-semibold transition sm:text-sm"
                        :class="activeCategory === item.value ? 'border-mentora-primary bg-mentora-primary text-white shadow-sm' : 'border-mentora-border bg-white text-mentora-navy hover:border-indigo-300 hover:bg-indigo-50'"
                        @click="changeCategory(item.value)"
                    >
                        <component :is="item.icon" :size="17" aria-hidden="true" />
                        {{ item.label }}
                    </button>
                </div>

                <div class="mt-8 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-mentora-primary">Jelajahi wawasan</p>
                        <h2 class="text-2xl font-extrabold text-mentora-navy sm:text-3xl">Artikel untuk Perjalanan Studimu</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ totalArticles }} artikel sesuai pencarian</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button v-if="hasFilters" type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-mentora-border bg-white px-4 py-2.5 text-sm font-semibold text-mentora-primary hover:bg-indigo-50" @click="clearFilters"><X :size="16" aria-hidden="true" /> Hapus Filter</button>
                        <Link v-if="preview" href="/artikel" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-mentora-primary shadow-sm hover:underline">Ke Katalog Asli <ArrowUpRight :size="16" aria-hidden="true" /></Link>
                    </div>
                </div>

                <div v-if="visibleArticles.length === 0" class="mt-7 flex flex-col items-center rounded-3xl border border-mentora-border bg-white px-6 py-16 text-center shadow-sm">
                    <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-indigo-50"><BookOpen :size="42" :stroke-width="1.5" class="text-mentora-primary" aria-hidden="true" /></div>
                    <h3 class="mt-6 text-xl font-bold text-mentora-navy">{{ hasFilters ? 'Artikel Tidak Ditemukan' : 'Belum Ada Artikel yang Ditampilkan' }}</h3>
                    <p class="mt-3 max-w-md text-sm leading-7 text-slate-600">{{ hasFilters ? 'Coba kata kunci atau kategori lain untuk menemukan bacaan yang kamu cari.' : 'Artikel yang telah dipublikasikan akan ditampilkan di sini. Sementara itu, kamu bisa menjelajahi peluang beasiswa.' }}</p>
                    <button v-if="hasFilters" type="button" class="mt-5 rounded-xl bg-mentora-primary px-6 py-3 text-sm font-bold text-white hover:bg-mentora-primary-hover" @click="clearFilters">Lihat Semua Artikel</button>
                    <Link v-else href="/beasiswa" class="mt-5 inline-flex min-h-11 items-center gap-2 rounded-xl bg-mentora-primary px-6 py-3 text-sm font-bold text-white hover:bg-mentora-primary-hover">Jelajahi Beasiswa <ArrowRight :size="16" aria-hidden="true" /></Link>
                </div>

                <template v-else>
                    <div class="mt-7 grid items-stretch gap-5 lg:grid-cols-[minmax(0,1.65fr)_minmax(290px,1fr)]">
                        <section v-if="featuredArticle" class="min-w-0" aria-label="Sorotan Artikel">
                            <div class="mb-3 flex items-center gap-2"><Sparkles :size="20" class="text-mentora-primary" aria-hidden="true" /><h3 class="text-lg font-bold text-mentora-navy">Sorotan Artikel</h3></div>
                            <article class="group relative flex min-h-[345px] flex-col overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-800 via-indigo-700 to-violet-700 p-7 text-white shadow-lg sm:min-h-[370px] sm:p-9">
                                <img v-if="featuredCover" :src="featuredCover" :alt="`Sampul artikel ${featuredArticle.title}`" class="absolute inset-0 h-full w-full object-cover opacity-35" />
                                <div class="pointer-events-none absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10" />
                                <div v-if="!featuredCover" class="pointer-events-none absolute bottom-4 right-4 rotate-[-10deg] text-white/20 sm:right-10" aria-hidden="true"><GraduationCap :size="174" :stroke-width="0.8" /></div>
                                <div class="relative z-10 flex h-full flex-1 flex-col items-start">
                                    <span class="rounded-full border border-white/30 bg-white/15 px-3 py-1.5 text-xs font-bold backdrop-blur-sm">{{ featuredArticle.category }}</span>
                                    <div class="mt-auto pt-10">
                                        <h4 class="max-w-xl text-2xl font-extrabold leading-tight sm:text-3xl">{{ featuredArticle.title }}</h4>
                                        <p class="mt-3 line-clamp-3 max-w-lg text-sm leading-7 text-white/85">{{ featuredArticle.excerpt || 'Baca artikel lengkapnya di MENTORA untuk mendapatkan informasi lebih lanjut.' }}</p>
                                        <p class="mt-4 inline-flex items-center gap-2 text-xs text-white/75"><CalendarDays :size="16" aria-hidden="true" />{{ formatDate(featuredArticle.published_at) }}</p>
                                        <Link v-if="!preview" :href="`/artikel/${encodeURIComponent(featuredArticle.slug)}`" class="mt-5 inline-flex min-h-11 items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-mentora-primary transition hover:bg-indigo-50">Baca Selengkapnya <ArrowRight :size="17" aria-hidden="true" /></Link>
                                        <span v-else class="mt-5 inline-flex rounded-xl border border-white/50 px-5 py-3 text-sm font-semibold text-white">Pratinjau desain — bukan artikel nyata</span>
                                    </div>
                                </div>
                            </article>
                        </section>

                        <aside v-if="sidebarArticles.length > 0" class="min-w-0">
                            <div class="mb-3 flex items-center gap-2"><BookMarked :size="20" class="text-mentora-primary" aria-hidden="true" /><h3 class="text-lg font-bold text-mentora-navy">Bacaan Lainnya</h3></div>
                            <div class="flex h-[370px] flex-col justify-around divide-y divide-mentora-border overflow-hidden rounded-3xl border border-mentora-border bg-white px-5 shadow-sm">
                                <article v-for="article in sidebarArticles" :key="article.id" class="flex min-w-0 items-start gap-3 py-4">
                                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-50 to-violet-100"><BookOpen :size="27" :stroke-width="1.5" class="text-mentora-primary" aria-hidden="true" /></div>
                                    <div class="min-w-0 flex-1">
                                        <p class="mb-1 truncate text-[11px] font-semibold text-mentora-primary">{{ article.category }}</p>
                                        <Link v-if="!preview" :href="`/artikel/${encodeURIComponent(article.slug)}`" class="line-clamp-2 text-sm font-bold leading-snug text-mentora-navy hover:text-mentora-primary hover:underline">{{ article.title }}</Link>
                                        <p v-else class="line-clamp-2 text-sm font-bold leading-snug text-mentora-navy">{{ article.title }}</p>
                                        <p class="mt-2 text-xs text-slate-500">{{ formatDate(article.published_at) }}</p>
                                    </div>
                                    <ChevronRight :size="18" class="mt-5 shrink-0 text-mentora-primary" aria-hidden="true" />
                                </article>
                            </div>
                        </aside>
                    </div>

                    <section v-if="remainingArticles.length > 0" class="mt-10">
                        <div class="mb-5 flex items-center gap-2"><BookOpen :size="22" class="text-mentora-primary" aria-hidden="true" /><h3 class="text-xl font-extrabold text-mentora-navy sm:text-2xl">Artikel Terbaru Lainnya</h3></div>
                        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            <ArticleCard v-for="article in remainingArticles" :key="article.id" :article="article" :preview="preview" />
                        </div>
                    </section>

                    <nav v-if="!preview && articles.last_page > 1" class="mt-10 flex items-center justify-center gap-4" aria-label="Navigasi halaman artikel">
                        <button type="button" :disabled="articles.current_page <= 1" class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-mentora-border bg-white px-4 py-2.5 text-sm font-semibold text-mentora-navy disabled:cursor-not-allowed disabled:opacity-40" @click="changePage(articles.current_page - 1)"><ChevronLeft :size="17" aria-hidden="true" /> Sebelumnya</button>
                        <span class="text-sm text-slate-600">{{ articles.current_page }} dari {{ articles.last_page }}</span>
                        <button type="button" :disabled="articles.current_page >= articles.last_page" class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-mentora-border bg-white px-4 py-2.5 text-sm font-semibold text-mentora-navy disabled:cursor-not-allowed disabled:opacity-40" @click="changePage(articles.current_page + 1)">Berikutnya <ChevronRight :size="17" aria-hidden="true" /></button>
                    </nav>
                </template>

                <section class="mt-12 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-violet-700 px-6 py-9 text-white sm:px-9">
                    <div class="flex flex-col items-start justify-between gap-5 md:flex-row md:items-center">
                        <div class="flex items-center gap-4"><div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15"><GraduationCap :size="33" aria-hidden="true" /></div><div><h2 class="text-xl font-extrabold">Terus Jelajahi Peluang Beasiswa</h2><p class="mt-1 max-w-xl text-sm leading-6 text-indigo-100">Temukan program yang sesuai dan siapkan langkah berikutnya bersama MENTORA.</p></div></div>
                        <Link href="/beasiswa" class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-mentora-primary hover:bg-indigo-50">Cari Beasiswa <ArrowRight :size="17" aria-hidden="true" /></Link>
                    </div>
                </section>
            </div>
        </main>
    </PublicLayout>
</template>
