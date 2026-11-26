<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowUpRight,
    BookOpen,
    CalendarDays,
    Clock3,
    FileText,
    GraduationCap,
    Info,
    ShieldCheck,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface ArticleItem {
    id: number;
    title: string;
    slug: string;
    category: string;
    excerpt: string | null;
    content: string;
    published_at: string | null;
    source_url: string | null;
    cover_image_path?: string | null;
}

const props = withDefaults(defineProps<{
    article: ArticleItem;
    preview?: boolean;
}>(), { preview: false });

const backUrl = computed(() => props.preview ? '/ui-preview/article-cards' : '/artikel');

const publishedDate = computed(() => {
    if (!props.article.published_at) return 'Belum diinformasikan';
    const date = new Date(props.article.published_at);
    if (Number.isNaN(date.getTime())) return 'Belum diinformasikan';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        timeZone: 'Asia/Jakarta',
    }).format(date);
});

const paragraphs = computed(() => {
    // Konten dari backend ditampilkan sebagai teks biasa (di-escape oleh Vue),
    // BUKAN HTML mentah. Baris kosong menjadi pemisah paragraf.
    const raw = props.article.content || '';
    return raw.split(/\n\s*\n/).map((p) => p.trim()).filter(Boolean);
});

const readingMinutes = computed(() => {
    const words = (props.article.content || '').trim().split(/\s+/).filter(Boolean).length;
    return Math.max(1, Math.ceil(words / 200));
});

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
    // Hanya jalur relatif public disk Laravel atau /storage/ yang valid.
    if (value.startsWith('//') || value.includes('..') || value.includes('?') || value.includes('#')) return null;
    if (value.startsWith('/storage/') && /^\/storage\/[a-zA-Z0-9/_\-.]+$/.test(value)) return value;
    if (/^[a-zA-Z0-9][a-zA-Z0-9/_\-.]*$/.test(value)) return `/storage/${value}`;
    return null;
}

const coverUrl = computed(() => safeCoverPath(props.article.cover_image_path));

const safeSourceUrl = computed(() => {
    if (!props.article.source_url || props.preview) return null;
    try {
        const url = new URL(props.article.source_url);
        return (url.protocol === 'https:' || url.protocol === 'http:') ? url.toString() : null;
    } catch {
        return null;
    }
});
</script>

<template>
    <PublicLayout>
        <Head :title="`${article.title} - Artikel MENTORA`" />

        <section class="min-h-screen bg-mentora-canvas pb-16 lg:pb-24">
            <div class="border-b border-indigo-100 bg-gradient-to-br from-indigo-50 via-slate-50 to-violet-50">
                <div class="mx-auto max-w-7xl px-4 pb-10 pt-9 sm:px-6 lg:px-8 lg:pb-14 lg:pt-12">
                    <div
                        v-if="preview"
                        class="mb-7 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-medium leading-6 text-amber-900"
                        role="note"
                    >
                        PREVIEW LOKAL — Artikel ini hanya contoh antarmuka, bukan artikel yang dipublikasikan MENTORA.
                    </div>

                    <Link :href="backUrl" class="inline-flex min-h-10 items-center gap-2 text-sm font-semibold text-mentora-primary hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary">
                        <ArrowLeft :size="17" aria-hidden="true" />
                        {{ preview ? 'Kembali ke Preview Artikel' : 'Kembali ke Daftar Artikel' }}
                    </Link>

                    <div class="mt-7 grid items-center gap-8 lg:grid-cols-[minmax(0,1fr)_19rem] lg:gap-12">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white px-3.5 py-2 text-xs font-bold text-mentora-primary shadow-sm">
                                    <BookOpen :size="16" aria-hidden="true" />
                                    {{ article.category }}
                                </span>
                                <span v-if="preview" class="rounded-full bg-amber-100 px-3 py-2 text-xs font-bold text-amber-900">Data Contoh</span>
                            </div>
                            <p class="mt-6 text-xs font-bold uppercase tracking-widest text-mentora-primary">MENTORA INSIGHTS</p>
                            <h1 class="mt-3 break-words text-3xl font-extrabold leading-tight tracking-tight text-mentora-navy sm:text-4xl lg:text-[2.7rem] lg:leading-[1.2]">
                                {{ article.title }}
                            </h1>
                            <p v-if="article.excerpt" class="mt-5 max-w-3xl text-base leading-8 text-slate-600 sm:text-lg">
                                {{ article.excerpt }}
                            </p>
                            <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-600">
                                <span class="inline-flex items-center gap-2"><CalendarDays :size="17" class="text-mentora-primary" aria-hidden="true" />{{ publishedDate }}</span>
                                <span class="inline-flex items-center gap-2"><Clock3 :size="17" class="text-mentora-primary" aria-hidden="true" />{{ readingMinutes }} menit baca</span>
                            </div>
                        </div>

                        <div class="relative flex min-h-52 items-center justify-center overflow-hidden rounded-3xl border border-indigo-100 bg-gradient-to-br from-indigo-100 via-sky-50 to-violet-100 shadow-sm sm:min-h-64">
                            <img v-if="coverUrl" :src="coverUrl" :alt="`Ilustrasi artikel ${article.title}`" class="absolute inset-0 h-full w-full object-cover" />
                            <template v-else>
                                <div class="absolute -right-10 -top-12 h-48 w-48 rounded-full bg-white/45" aria-hidden="true" />
                                <div class="absolute -bottom-16 -left-10 h-40 w-40 rounded-full bg-indigo-300/25" aria-hidden="true" />
                                <div class="relative flex h-32 w-32 rotate-[-7deg] items-center justify-center rounded-3xl border border-white/80 bg-white/75 shadow-lg">
                                    <BookOpen :size="70" :stroke-width="1.4" class="text-mentora-primary" aria-hidden="true" />
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto grid max-w-7xl items-start gap-7 px-4 pt-9 sm:px-6 lg:grid-cols-[minmax(0,1fr)_19rem] lg:gap-8 lg:px-8 lg:pt-12">
                <article class="min-w-0 rounded-3xl border border-mentora-border bg-white px-5 py-7 shadow-sm sm:px-8 sm:py-10 lg:px-10">
                    <div class="mb-7 flex items-center gap-3 border-b border-mentora-border pb-6">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-mentora-primary"><FileText :size="23" aria-hidden="true" /></span>
                        <div>
                            <h2 class="text-xl font-bold text-mentora-navy">Isi Artikel</h2>
                            <p class="mt-1 text-xs text-slate-500">Informasi dan panduan untuk pembaca MENTORA</p>
                        </div>
                    </div>

                    <div v-if="paragraphs.length" class="space-y-5 break-words text-[15px] leading-8 text-slate-700 sm:text-base sm:leading-9">
                        <p v-for="(paragraph, index) in paragraphs" :key="index" class="whitespace-pre-line">{{ paragraph }}</p>
                    </div>
                    <p v-else class="text-sm leading-7 text-slate-600">Isi artikel belum tersedia.</p>

                    <div v-if="safeSourceUrl" class="mt-9 border-t border-mentora-border pt-7">
                        <h3 class="text-sm font-bold text-mentora-navy">Referensi atau Sumber Resmi</h3>
                        <a :href="safeSourceUrl" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex max-w-full items-start gap-2 break-all text-sm font-semibold leading-6 text-mentora-primary hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary">
                            {{ safeSourceUrl }} <ArrowUpRight :size="17" class="mt-0.5 shrink-0" aria-hidden="true" />
                        </a>
                    </div>

                    <div class="mt-9 border-t border-mentora-border pt-7">
                        <Link :href="backUrl" class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-5 py-3 text-sm font-bold text-mentora-primary transition hover:bg-indigo-100">
                            <ArrowLeft :size="17" aria-hidden="true" />
                            {{ preview ? 'Kembali ke Preview Artikel' : 'Lihat Artikel Lainnya' }}
                        </Link>
                    </div>
                </article>

                <aside class="space-y-5 lg:sticky lg:top-24" aria-label="Informasi pendukung artikel">
                    <section class="rounded-2xl border border-mentora-border bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-mentora-navy"><Info :size="19" class="text-mentora-primary" aria-hidden="true" /> Tentang Artikel</h2>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div><dt class="text-xs text-slate-500">Kategori</dt><dd class="mt-1 font-semibold text-mentora-navy">{{ article.category }}</dd></div>
                            <div class="border-t border-mentora-border pt-4"><dt class="text-xs text-slate-500">Tanggal Publikasi</dt><dd class="mt-1 font-semibold text-mentora-navy">{{ publishedDate }}</dd></div>
                            <div class="border-t border-mentora-border pt-4"><dt class="text-xs text-slate-500">Estimasi Waktu Baca</dt><dd class="mt-1 font-semibold text-mentora-navy">{{ readingMinutes }} menit</dd></div>
                        </dl>
                    </section>

                    <section class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-white p-6 shadow-sm">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-mentora-primary shadow-sm"><GraduationCap :size="24" aria-hidden="true" /></div>
                        <h2 class="mt-4 text-lg font-bold leading-snug text-mentora-navy">Temukan Peluang Beasiswa</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Cari program beasiswa sesuai kebutuhan dan jenjang pendidikanmu.</p>
                        <Link href="/beasiswa" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-mentora-primary px-4 py-3 text-sm font-bold text-white transition hover:bg-mentora-primary-hover">Jelajahi Beasiswa <ArrowUpRight :size="17" aria-hidden="true" /></Link>
                    </section>

                    <section class="rounded-2xl border border-mentora-border bg-white p-5 shadow-sm">
                        <div class="flex gap-3 text-sm leading-6 text-slate-600"><ShieldCheck :size="20" class="mt-0.5 shrink-0 text-mentora-primary" aria-hidden="true" /><p>{{ preview ? 'Ini adalah artikel demonstrasi untuk memeriksa desain halaman.' : 'Periksa selalu informasi beasiswa dan ketentuan melalui sumber resmi penyelenggara.' }}</p></div>
                    </section>
                </aside>
            </div>
        </section>
    </PublicLayout>
</template>
