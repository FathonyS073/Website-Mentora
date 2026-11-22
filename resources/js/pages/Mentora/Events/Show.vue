<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowUpRight,
    CalendarDays,
    CalendarRange,
    Clock3,
    FileText,
    Globe2,
    MapPin,
    MonitorPlay,
    UsersRound,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';

/** Data asli dikirim oleh EventDetailController melalui Inertia. */
interface EventItem {
    id: number;
    title: string;
    slug: string;
    organizer_name: string;
    summary: string | null;
    description: string | null;
    event_type: string;
    delivery_mode: string;
    location_name: string | null;
    timezone: string;
    starts_at: string;
    ends_at: string | null;
    registration_closes_at: string | null;
    source_url: string | null;
    registration_url: string | null;
    cover_image_path?: string | null;
}

const props = withDefaults(defineProps<{
    event: EventItem;
    preview?: boolean;
}>(), {
    preview: false,
});

const eventTypeLabels: Record<string, string> = {
    webinar: 'Webinar',
    seminar: 'Seminar',
    workshop: 'Workshop',
    other: 'Kegiatan Lainnya',
};

const deliveryModeLabels: Record<string, string> = {
    online: 'Online',
    offline: 'Offline',
    hybrid: 'Hybrid',
};

const eventTypeLabel = computed(() =>
    eventTypeLabels[props.event.event_type] ?? props.event.event_type
);

const deliveryModeLabel = computed(() =>
    deliveryModeLabels[props.event.delivery_mode] ?? props.event.delivery_mode
);

/** Periksa zona waktu agar kode tidak error jika data sumber tidak valid. */
const displayTimezone = computed(() => {
    const candidate = props.event.timezone || 'Asia/Jakarta';
    try {
        new Intl.DateTimeFormat('id-ID', { timeZone: candidate });
        return candidate;
    } catch {
        return 'Asia/Jakarta';
    }
});

const timezoneLabel = computed(() => {
    const labels: Record<string, string> = {
        'Asia/Jakarta': 'WIB',
        'Asia/Makassar': 'WITA',
        'Asia/Jayapura': 'WIT',
    };
    return labels[displayTimezone.value] ?? displayTimezone.value;
});

/** Jadwal dari backend adalah ISO datetime; tampilkan dalam zona waktu event. */
function formatDateTime(value: string | null): string {
    if (!value) return 'Belum diinformasikan';

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return 'Belum diinformasikan';

    const formatted = new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: displayTimezone.value,
    }).format(date);

    return `${formatted} ${timezoneLabel.value}`;
}

/** Hindari skema tautan berbahaya (javascript:, data:, dll.). */
function safeExternalUrl(value: string | null): string | null {
    if (!value) return null;
    try {
        const url = new URL(value);
        return ['http:', 'https:'].includes(url.protocol) ? url.toString() : null;
    } catch {
        return null;
    }
}

const sourceUrl = computed(() => safeExternalUrl(props.event.source_url));
const registrationUrl = computed(() => safeExternalUrl(props.event.registration_url));

const locationLabel = computed(() => {
    if (props.event.location_name) return props.event.location_name;
    if (props.event.delivery_mode === 'online') return 'Dilaksanakan secara online';
    return 'Lokasi belum diinformasikan';
});
</script>

<template>
    <PublicLayout>
        <Head :title="`${event.title} - Event MENTORA`" />

        <section class="bg-mentora-canvas py-10 lg:py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Preview hanya dari route lokal; tidak pernah berasal dari database. -->
                <div
                    v-if="preview"
                    role="note"
                    class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold leading-6 text-amber-900"
                >
                    PREVIEW LOKAL — Seluruh informasi ini hanya contoh antarmuka,
                    bukan kegiatan atau pendaftaran Event yang nyata.
                </div>

                <Link
                    href="/event"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-mentora-primary hover:underline"
                >
                    <ArrowLeft :size="17" aria-hidden="true" />
                    Kembali ke Katalog Event
                </Link>

                <!-- Identitas dan ringkasan event -->
                <header class="mt-6 overflow-hidden rounded-2xl border border-mentora-border bg-white shadow-sm">
                    <div class="flex min-h-36 items-center justify-center bg-gradient-to-r from-indigo-50 via-blue-50 to-violet-100 p-6">
                        <CalendarRange :size="72" :stroke-width="1.3" class="text-mentora-primary/70" aria-hidden="true" />
                    </div>
                    <div class="p-6 md:p-8">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-mentora-primary">
                                MENTORA Event Information
                            </p>
                            <span
                                v-if="preview"
                                class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700"
                            >
                                Data Contoh
                            </span>
                            <span
                                v-else
                                class="rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-mentora-primary"
                            >
                                Event Dipublikasikan
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-semibold text-mentora-primary">
                            {{ event.organizer_name }}
                        </p>
                        <h1 class="mt-2 text-2xl font-bold leading-tight text-mentora-navy sm:text-3xl lg:text-4xl">
                            {{ event.title }}
                        </h1>
                        <p class="mt-4 max-w-3xl leading-7 text-slate-600">
                            {{ event.summary || 'Ringkasan kegiatan belum tersedia.' }}
                        </p>

                        <div class="mt-7 grid gap-3 sm:grid-cols-3">
                            <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-4">
                                <CalendarDays :size="20" class="shrink-0 text-mentora-primary" aria-hidden="true" />
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500">Jenis Kegiatan</p>
                                    <p class="mt-1 text-sm font-semibold text-mentora-navy">{{ eventTypeLabel }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-4">
                                <MonitorPlay :size="20" class="shrink-0 text-mentora-primary" aria-hidden="true" />
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500">Metode Pelaksanaan</p>
                                    <p class="mt-1 text-sm font-semibold text-mentora-navy">{{ deliveryModeLabel }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-4">
                                <Globe2 :size="20" class="shrink-0 text-mentora-primary" aria-hidden="true" />
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500">Zona Waktu Kegiatan</p>
                                    <p class="mt-1 text-sm font-semibold text-mentora-navy">{{ timezoneLabel }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Kolom utama dan panel informasi resmi -->
                <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <div class="min-w-0 space-y-6">
                        <section class="rounded-2xl border border-mentora-border bg-white p-6 shadow-sm md:p-8">
                            <div class="flex items-center gap-3">
                                <FileText :size="22" class="text-mentora-primary" aria-hidden="true" />
                                <h2 class="text-xl font-bold text-mentora-navy">Tentang Event</h2>
                            </div>
                            <p class="mt-4 whitespace-pre-line break-words leading-7 text-slate-600">
                                {{ event.description || 'Deskripsi lengkap kegiatan belum tersedia. Silakan periksa sumber resmi penyelenggara.' }}
                            </p>
                        </section>

                        <section class="rounded-2xl border border-mentora-border bg-white p-6 shadow-sm md:p-8">
                            <div class="flex items-center gap-3">
                                <MapPin :size="22" class="text-mentora-primary" aria-hidden="true" />
                                <h2 class="text-xl font-bold text-mentora-navy">Lokasi dan Pelaksanaan</h2>
                            </div>
                            <dl class="mt-5 space-y-4 text-sm">
                                <div>
                                    <dt class="font-medium text-slate-500">Metode kegiatan</dt>
                                    <dd class="mt-1 font-semibold text-mentora-navy">{{ deliveryModeLabel }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-slate-500">Lokasi atau media pelaksanaan</dt>
                                    <dd class="mt-1 break-words text-mentora-navy">{{ locationLabel }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-slate-500">Penyelenggara</dt>
                                    <dd class="mt-1 break-words text-mentora-navy">{{ event.organizer_name }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section class="rounded-2xl border border-mentora-border bg-white p-6 shadow-sm md:p-8">
                            <h2 class="text-xl font-bold text-mentora-navy">Informasi Penting</h2>
                            <p class="mt-4 text-sm leading-7 text-slate-600">
                                MENTORA menyediakan informasi kegiatan pendidikan dan beasiswa.
                                Jadwal, ketentuan peserta, ketersediaan tempat, dan proses pendaftaran
                                dapat berubah sesuai keputusan penyelenggara. Selalu periksa website resmi.
                            </p>
                        </section>
                    </div>

                    <aside class="space-y-5 lg:sticky lg:top-24">
                        <section class="rounded-2xl border border-mentora-border bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-mentora-navy">Jadwal dan Pendaftaran</h2>

                            <dl class="mt-5 space-y-5">
                                <div class="flex items-start gap-3">
                                    <CalendarDays :size="19" class="mt-0.5 shrink-0 text-mentora-primary" aria-hidden="true" />
                                    <div>
                                        <dt class="text-xs text-slate-500">Mulai Kegiatan</dt>
                                        <dd class="mt-1 text-sm font-semibold text-mentora-navy">{{ formatDateTime(event.starts_at) }}</dd>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <Clock3 :size="19" class="mt-0.5 shrink-0 text-mentora-primary" aria-hidden="true" />
                                    <div>
                                        <dt class="text-xs text-slate-500">Selesai Kegiatan</dt>
                                        <dd class="mt-1 text-sm font-semibold text-mentora-navy">{{ formatDateTime(event.ends_at) }}</dd>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <UsersRound :size="19" class="mt-0.5 shrink-0 text-mentora-primary" aria-hidden="true" />
                                    <div>
                                        <dt class="text-xs text-slate-500">Batas Pendaftaran</dt>
                                        <dd class="mt-1 text-sm font-semibold text-mentora-navy">{{ formatDateTime(event.registration_closes_at) }}</dd>
                                    </div>
                                </div>
                            </dl>

                            <div class="mt-6 space-y-3">
                                <a
                                    v-if="registrationUrl && !preview"
                                    :href="registrationUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-mentora-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-mentora-primary-hover"
                                >
                                    Buka Pendaftaran Resmi
                                    <ArrowUpRight :size="17" aria-hidden="true" />
                                </a>
                                <a
                                    v-if="sourceUrl && !preview"
                                    :href="sourceUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex min-h-11 w-full items-center justify-between gap-2 rounded-xl border border-mentora-border bg-white px-4 py-3 text-sm font-semibold text-mentora-navy transition hover:border-mentora-primary hover:bg-indigo-50"
                                >
                                    <span class="min-w-0">
                                        <span class="block">Website Resmi Event</span>
                                        <span class="mt-1 block break-all text-xs font-normal text-slate-500">{{ sourceUrl }}</span>
                                    </span>
                                    <ArrowUpRight :size="17" class="shrink-0 text-mentora-primary" aria-hidden="true" />
                                </a>
                                <p v-if="preview" class="rounded-xl bg-amber-50 p-3 text-sm leading-6 text-amber-800">
                                    Link pendaftaran dinonaktifkan pada data contoh.
                                </p>
                                <p v-else-if="!registrationUrl && !sourceUrl" class="text-sm leading-6 text-slate-500">
                                    Tautan pendaftaran dan sumber resmi belum tersedia.
                                </p>
                            </div>

                            <p class="mt-5 rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-500">
                                Pastikan jadwal dan persyaratan terbaru melalui sumber resmi penyelenggara.
                            </p>
                        </section>
                    </aside>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
