<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    BookOpen,
    CalendarDays,
    ClipboardList,
    FileText,
    GraduationCap,
    Lightbulb,
    Megaphone,
    Sparkles,
} from 'lucide-vue-next';

interface ArticleItem {
    id: number;
    title: string;
    slug: string;
    category: string;
    excerpt: string | null;
    published_at: string | null;
    cover_image_path?: string | null;
}

const props = withDefaults(defineProps<{
    article: ArticleItem;
    preview?: boolean;
}>(), { preview: false });

// Hindari menampilkan skema URL tidak aman dari data CMS.
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
    // Penyimpanan Laravel public disk: "articles/example.webp" atau "/storage/articles/example.webp".
    if (value.startsWith('//') || value.includes('..') || value.includes('?') || value.includes('#')) return null;
    if (value.startsWith('/storage/') && /^\/storage\/[a-zA-Z0-9/_\-.]+$/.test(value)) return value;
    if (/^[a-zA-Z0-9][a-zA-Z0-9/_\-.]*$/.test(value)) return `/storage/${value}`;
    return null;
}

const coverUrl = computed(() => safeCoverPath(props.article.cover_image_path));
const detailUrl = computed(() => `/artikel/${encodeURIComponent(props.article.slug)}`);

const categoryIcon = computed(() => {
    const category = props.article.category.toLowerCase();
    if (category.includes('tips')) return GraduationCap;
    if (category.includes('daftar') || category.includes('panduan')) return ClipboardList;
    if (category.includes('seleksi')) return FileText;
    if (category.includes('pengembangan')) return Lightbulb;
    if (category.includes('update') || category.includes('info')) return Megaphone;
    if (category.includes('kisah') || category.includes('inspir')) return Sparkles;
    return BookOpen;
});

const coverTheme = computed(() => {
    const category = props.article.category.toLowerCase();
    if (category.includes('seleksi')) return 'from-sky-100 via-blue-50 to-indigo-100';
    if (category.includes('pengembangan')) return 'from-amber-100 via-orange-50 to-rose-100';
    if (category.includes('kisah') || category.includes('inspir')) return 'from-violet-100 via-fuchsia-50 to-indigo-100';
    if (category.includes('info') || category.includes('update')) return 'from-emerald-100 via-teal-50 to-cyan-100';
    return 'from-indigo-100 via-blue-50 to-violet-100';
});

const publishDate = computed(() => {
    if (!props.article.published_at) return 'Tanggal belum tersedia';
    const date = new Date(props.article.published_at);
    if (Number.isNaN(date.getTime())) return 'Tanggal belum tersedia';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric', timeZone: 'Asia/Jakarta',
    }).format(date);
});
</script>

<template>
    <article class="group flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-mentora-border bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="relative h-44 overflow-hidden bg-gradient-to-br" :class="coverTheme">
            <img
                v-if="coverUrl"
                :src="coverUrl"
                :alt="`Sampul artikel ${article.title}`"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                loading="lazy"
            />
            <div v-else class="flex h-full items-center justify-center" aria-hidden="true">
                <div class="absolute -right-8 -top-12 h-40 w-40 rounded-full bg-white/35" />
                <div class="absolute -bottom-12 -left-6 h-32 w-32 rounded-full bg-indigo-300/20" />
                <div class="relative flex h-20 w-20 rotate-[-7deg] items-center justify-center rounded-3xl border border-white/80 bg-white/75 shadow-lg backdrop-blur-sm">
                    <component :is="categoryIcon" :size="43" :stroke-width="1.5" class="text-mentora-primary" />
                </div>
            </div>
            <span class="absolute left-4 top-4 max-w-[calc(100%-2rem)] truncate rounded-full border border-white/70 bg-white/95 px-3 py-1.5 text-xs font-semibold text-mentora-primary shadow-sm">
                {{ article.category }}
            </span>
        </div>

        <div class="flex flex-1 flex-col p-5">
            <h3 class="line-clamp-2 text-lg font-bold leading-snug text-mentora-navy">
                {{ article.title }}
            </h3>
            <p class="mt-3 line-clamp-3 flex-1 text-sm leading-6 text-slate-600">
                {{ article.excerpt || 'Baca informasi selengkapnya melalui halaman artikel MENTORA.' }}
            </p>
            <div class="mt-5 flex items-center justify-between gap-2 border-t border-mentora-border pt-4">
                <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                    <CalendarDays :size="15" aria-hidden="true" />
                    {{ publishDate }}
                </span>
                <Link
                    v-if="!preview"
                    :href="detailUrl"
                    :aria-label="`Baca artikel ${article.title}`"
                    class="inline-flex items-center gap-1 text-sm font-bold text-mentora-primary transition hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-mentora-primary"
                >
                    Baca <ArrowUpRight :size="17" aria-hidden="true" />
                </Link>
                <span v-else class="text-xs font-semibold text-mentora-primary">Contoh desain</span>
            </div>
        </div>
    </article>
</template>
