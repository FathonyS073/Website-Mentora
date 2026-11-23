<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
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
}

const props = defineProps<{ article: ArticleItem }>();

const publishedDate = computed(() => {
    if (!props.article.published_at) return 'Tanggal belum diinformasikan';
    const date = new Date(props.article.published_at);
    if (Number.isNaN(date.getTime())) return 'Tanggal belum diinformasikan';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta',
    }).format(date);
});

const safeSourceUrl = computed(() => {
    if (!props.article.source_url) return null;
    try {
        const url = new URL(props.article.source_url);
        return ['https:', 'http:'].includes(url.protocol) ? url.toString() : null;
    } catch {
        return null;
    }
});
</script>

<template>
    <PublicLayout>
        <Head :title="`${article.title} - Artikel MENTORA`" />
        <!-- Fondasi detail; desain final pada S1-04.5F. Konten ditampilkan sebagai teks aman, bukan raw HTML. -->
        <section class="bg-mentora-canvas py-12 lg:py-16">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <Link href="/artikel" class="text-sm font-semibold text-mentora-primary hover:underline">← Kembali ke Daftar Artikel</Link>

                <article class="mt-6 rounded-2xl border border-mentora-border bg-white p-6 shadow-sm md:p-10">
                    <p class="text-sm font-semibold uppercase tracking-wide text-mentora-primary">{{ article.category }}</p>
                    <h1 class="mt-3 text-3xl font-bold leading-tight text-mentora-navy md:text-4xl">{{ article.title }}</h1>
                    <p class="mt-4 text-sm text-slate-500">Diterbitkan: {{ publishedDate }}</p>
                    <p v-if="article.excerpt" class="mt-6 text-lg leading-8 text-slate-700">{{ article.excerpt }}</p>
                    <hr class="my-8 border-mentora-border" />
                    <div class="whitespace-pre-line break-words text-base leading-8 text-slate-700">{{ article.content }}</div>

                    <a v-if="safeSourceUrl" :href="safeSourceUrl" target="_blank" rel="noopener noreferrer" class="mt-8 inline-flex rounded-xl border border-mentora-border px-5 py-3 text-sm font-semibold text-mentora-primary hover:border-mentora-primary">Lihat Sumber Referensi ↗</a>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>
