<script setup lang="ts">
import { computed } from 'vue';
import { Bookmark, CalendarDays, ExternalLink, GraduationCap, MapPin } from 'lucide-vue-next';

interface ScholarshipItem {
    id: number;
    title: string;
    slug: string;
    provider_name: string;
    summary: string | null;
    education_levels: string[];
    funding_type: string;
    location_scope: string;
    opens_at?: string | null;
    closes_at?: string | null;
    source_url?: string | null;
}

const props = defineProps<{ scholarship: ScholarshipItem }>();

function dateOnly(value?: string | null): string | null {
    if (!value) return null;
    const match = /^\d{4}-\d{2}-\d{2}/.exec(value);
    return match?.[0] ?? null;
}

function todayLocal(): string {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatDate(value?: string | null): string {
    const normalized = dateOnly(value);
    if (!normalized) return 'Belum dicantumkan';
    const date = new Date(`${normalized}T12:00:00`);
    if (Number.isNaN(date.getTime())) return 'Tanggal belum valid';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    }).format(date);
}

const scopeLabel = computed(() => {
    switch (props.scholarship.location_scope) {
        case 'indonesia': return 'Dalam Negeri';
        case 'international': return 'Luar Negeri';
        default: return 'Cakupan belum ditetapkan';
    }
});

const fundingLabel = computed(() => {
    switch (props.scholarship.funding_type) {
        case 'fully_funded': return 'Fully Funded';
        case 'partially_funded': return 'Partially Funded';
        default: return 'Pendanaan lainnya';
    }
});

const registration = computed(() => {
    const opens = dateOnly(props.scholarship.opens_at);
    const closes = dateOnly(props.scholarship.closes_at);
    const today = todayLocal();

    if (closes && closes < today) {
        return { label: 'Pendaftaran ditutup', color: 'bg-slate-100 text-slate-600' };
    }
    if (opens && opens > today) {
        return { label: 'Belum dibuka', color: 'bg-amber-50 text-amber-700' };
    }
    if (opens && closes && opens <= today && closes >= today) {
        return { label: 'Pendaftaran dibuka', color: 'bg-emerald-50 text-emerald-700' };
    }
    return { label: 'Jadwal belum lengkap', color: 'bg-slate-100 text-slate-600' };
});

const safeSource = computed(() => {
    if (!props.scholarship.source_url) return null;
    try {
        const url = new URL(props.scholarship.source_url);
        return ['https:', 'http:'].includes(url.protocol) ? url.href : null;
    } catch {
        return null;
    }
});
</script>

<template>
    <article
        class="grid gap-4 rounded-2xl border border-mentora-border bg-white p-4 shadow-sm transition-shadow hover:shadow-md sm:p-5 lg:grid-cols-[112px_minmax(0,1fr)_175px_135px] lg:items-center lg:gap-5"
    >
        <!-- Identitas penyelenggara; logo resmi bisa ditambahkan saat data tersedia. -->
        <div
            class="flex h-24 w-full items-center justify-center rounded-xl border border-mentora-border bg-mentora-canvas text-mentora-primary sm:h-28 lg:h-24"
            aria-hidden="true"
        >
            <GraduationCap :size="36" :stroke-width="1.7" />
        </div>

        <div class="min-w-0">
            <h2 class="text-base font-bold leading-snug text-mentora-navy sm:text-lg">
                {{ scholarship.title }}
            </h2>
            <p class="mt-1 text-xs font-medium text-mentora-muted">
                {{ scholarship.provider_name }}
            </p>
            <div class="mt-3 flex flex-wrap gap-1.5">
                <span
                    v-for="level in scholarship.education_levels"
                    :key="level"
                    class="rounded-full bg-mentora-lavender px-2.5 py-1 text-xs font-semibold text-mentora-primary"
                >{{ level }}</span>
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700">
                    <MapPin :size="12" aria-hidden="true" /> {{ scopeLabel }}
                </span>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs text-indigo-700">
                    {{ fundingLabel }}
                </span>
            </div>
            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">
                {{ scholarship.summary || 'Ringkasan program belum tersedia.' }}
            </p>
        </div>

        <div class="border-t border-mentora-border pt-4 lg:border-l lg:border-t-0 lg:py-2 lg:pl-4">
            <p class="inline-flex items-center gap-2 text-xs text-mentora-muted">
                <CalendarDays :size="15" class="text-mentora-primary" aria-hidden="true" />
                Batas Pendaftaran
            </p>
            <p class="mt-2 text-sm font-bold text-mentora-navy">
                {{ formatDate(scholarship.closes_at) }}
            </p>
            <span :class="registration.color" class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                {{ registration.label }}
            </span>
        </div>

        <div class="flex flex-col gap-2 border-t border-mentora-border pt-4 lg:border-l lg:border-t-0 lg:py-2 lg:pl-4">
            <a
                v-if="safeSource"
                :href="safeSource"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-mentora-primary px-3 text-xs font-semibold text-white transition hover:bg-mentora-primary-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary"
                :aria-label="`Buka sumber resmi untuk ${scholarship.title} di tab baru`"
            >
                Sumber Resmi <ExternalLink :size="14" aria-hidden="true" />
            </a>
            <span v-else class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-3 text-center text-xs text-slate-500">
                Sumber belum tersedia
            </span>
            <button
                type="button"
                disabled
                title="Fitur simpan beasiswa akan tersedia pada tahap pengembangan Member"
                class="inline-flex min-h-9 cursor-not-allowed items-center justify-center gap-1.5 text-xs text-slate-400"
            >
                <Bookmark :size="15" aria-hidden="true" /> Simpan · segera hadir
            </button>
        </div>
    </article>
</template>
