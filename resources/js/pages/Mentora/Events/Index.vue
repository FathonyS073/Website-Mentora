<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import {
    ArrowRight,
    CalendarDays,
    CalendarX2,
    Search,
    SlidersHorizontal,
    Sparkles,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
import EventCard from '@/components/mentora/EventCard.vue';

interface EventItem {
    id: number;
    title: string;
    slug: string;
    organizer_name: string;
    summary: string | null;
    event_type: string;
    delivery_mode: string;
    location_name: string | null;
    starts_at: string;
}

interface PaginatedEvents {
    items: EventItem[];
    total: number;
    current_page: number;
    last_page: number;
    per_page: number;
}

const props = defineProps<{
    events: PaginatedEvents;
    filters: {
        search: string;
        event_type: string;
        delivery_mode: string;
    };
}>();

const keyword = ref(props.filters.search ?? '');
const eventType = ref(props.filters.event_type ?? '');
const deliveryMode = ref(props.filters.delivery_mode ?? '');

const types = [
    { value: '', label: 'Semua Event' },
    { value: 'webinar', label: 'Webinar' },
    { value: 'seminar', label: 'Seminar' },
    { value: 'workshop', label: 'Workshop' },
    { value: 'other', label: 'Lainnya' },
];

const hasActiveFilters = computed(() =>
    Boolean(props.filters.search || props.filters.event_type || props.filters.delivery_mode)
);

function paramsFor(page = 1): Record<string, string | number> {
    const params: Record<string, string | number> = {};
    const search = keyword.value.trim();

    if (search) params.search = search;
    if (eventType.value) params.event_type = eventType.value;
    if (deliveryMode.value) params.delivery_mode = deliveryMode.value;
    if (page > 1) params.page = page;

    return params;
}

function searchEvents(): void {
    // Tanpa preserveState agar filter kembali sinkron dengan URL/history.
    router.get('/event', paramsFor(), {
        preserveState: false,
        replace: true,
    });
}

function chooseType(type: string): void {
    eventType.value = type;
    searchEvents();
}

function clearFilters(): void {
    keyword.value = '';
    eventType.value = '';
    deliveryMode.value = '';
    router.get('/event', {}, {
        preserveState: false,
        replace: true,
    });
}

function goToPage(page: number): void {
    if (page < 1 || page > props.events.last_page) return;
    router.get('/event', paramsFor(page), {
        preserveState: false,
        preserveScroll: true,
    });
}
</script>

<template>
    <PublicLayout>
        <Head title="Event Pendidikan dan Beasiswa - MENTORA" />

        <!-- Hero: bahasa dan warna mengikuti gaya halaman publik MENTORA. -->
        <section class="bg-mentora-canvas pt-10 sm:pt-14 lg:pt-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl border border-mentora-border bg-gradient-to-br from-white via-indigo-50 to-violet-100 p-7 sm:p-10 lg:p-12">
                    <div class="relative z-10 max-w-2xl">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-mentora-primary shadow-sm">
                            <CalendarDays :size="16" aria-hidden="true" />
                            MENTORA EVENTS
                        </span>
                        <h1 class="mt-5 text-3xl font-bold tracking-tight text-mentora-navy sm:text-4xl lg:text-5xl">
                            Temukan Event Edukatif untuk Masa Depanmu
                        </h1>
                        <p class="mt-4 max-w-xl text-sm leading-7 text-slate-600 sm:text-base">
                            Cari webinar, seminar, workshop, dan kegiatan pendidikan untuk membantumu mempersiapkan peluang beasiswa.
                        </p>
                        <a
                            href="#daftar-event"
                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-mentora-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-mentora-primary-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary"
                        >
                            Jelajahi Event
                            <ArrowRight :size="17" aria-hidden="true" />
                        </a>
                    </div>
                    <div class="pointer-events-none absolute -bottom-12 -right-10 hidden h-64 w-64 items-center justify-center rounded-full border border-indigo-200 bg-white/40 text-mentora-primary/50 sm:flex lg:right-12 lg:h-80 lg:w-80" aria-hidden="true">
                        <CalendarDays :size="120" :stroke-width="1" />
                    </div>
                </div>
            </div>
        </section>

        <section id="daftar-event" class="scroll-mt-24 bg-mentora-canvas py-10 lg:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filter utama tetap menggunakan query parameter backend yang ada. -->
                <form
                    class="grid gap-4 rounded-2xl border border-mentora-border bg-white p-5 shadow-sm md:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end lg:p-6"
                    @submit.prevent="searchEvents"
                >
                    <div class="min-w-0">
                        <label for="event-search" class="mb-2 block text-sm font-semibold text-mentora-navy">Kata Kunci</label>
                        <div class="relative">
                            <Search :size="18" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true" />
                            <input
                                id="event-search"
                                v-model="keyword"
                                type="search"
                                maxlength="150"
                                placeholder="Judul event atau penyelenggara..."
                                class="min-h-12 w-full min-w-0 rounded-xl border border-mentora-border bg-white py-3 pl-11 pr-4 text-sm text-mentora-navy outline-none focus:border-mentora-primary"
                            />
                        </div>
                    </div>
                    <div class="min-w-0">
                        <label for="event-type" class="mb-2 block text-sm font-semibold text-mentora-navy">Jenis Kegiatan</label>
                        <select
                            id="event-type"
                            v-model="eventType"
                            class="min-h-12 w-full rounded-xl border border-mentora-border bg-white px-3 py-3 text-sm text-mentora-navy outline-none focus:border-mentora-primary"
                        >
                            <option v-for="type in types" :key="type.value" :value="type.value">{{ type.label }}</option>
                        </select>
                    </div>
                    <div class="min-w-0">
                        <label for="event-mode" class="mb-2 block text-sm font-semibold text-mentora-navy">Metode</label>
                        <select
                            id="event-mode"
                            v-model="deliveryMode"
                            class="min-h-12 w-full rounded-xl border border-mentora-border bg-white px-3 py-3 text-sm text-mentora-navy outline-none focus:border-mentora-primary"
                        >
                            <option value="">Semua Metode</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-mentora-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-mentora-primary-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary md:col-span-2 lg:col-span-1"
                    >
                        <SlidersHorizontal :size="17" aria-hidden="true" />
                        Cari Event
                    </button>
                </form>

                <!-- Shortcut jenis kegiatan (tetap terhubung dengan filter database). -->
                <div class="mt-6 flex flex-wrap items-center gap-2" aria-label="Filter cepat jenis event">
                    <button
                        v-for="type in types"
                        :key="type.value"
                        type="button"
                        :aria-pressed="eventType === type.value"
                        class="rounded-full border px-4 py-2 text-sm font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary"
                        :class="eventType === type.value
                            ? 'border-mentora-primary bg-mentora-primary text-white'
                            : 'border-mentora-border bg-white text-slate-600 hover:border-mentora-primary hover:text-mentora-primary'"
                        @click="chooseType(type.value)"
                    >
                        {{ type.label }}
                    </button>
                </div>

                <div class="mt-10 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-mentora-primary">
                            <Sparkles :size="16" aria-hidden="true" />
                            DAFTAR EVENT
                        </p>
                        <h2 class="mt-2 text-2xl font-bold text-mentora-navy">Jelajahi Kegiatan</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ events.total }} event dipublikasikan sesuai pencarian</p>
                    </div>
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        class="rounded-xl border border-mentora-border bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:border-mentora-primary hover:text-mentora-primary"
                        @click="clearFilters"
                    >
                        Hapus Filter
                    </button>
                </div>

                <div
                    v-if="events.items.length === 0"
                    class="mt-6 flex flex-col items-center rounded-2xl border border-mentora-border bg-white px-6 py-14 text-center shadow-sm sm:py-16"
                >
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-mentora-primary">
                        <CalendarX2 :size="30" aria-hidden="true" />
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-mentora-navy">
                        {{ hasActiveFilters ? 'Event Tidak Ditemukan' : 'Belum Ada Event yang Ditampilkan' }}
                    </h3>
                    <p class="mt-3 max-w-lg text-sm leading-6 text-slate-600">
                        {{ hasActiveFilters
                            ? 'Belum ada event terpublikasi yang sesuai dengan filter ini. Coba kata kunci atau kategori lainnya.'
                            : 'Informasi event yang telah dipublikasikan akan tampil di sini. Silakan kembali memeriksa secara berkala.' }}
                    </p>
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        class="mt-5 rounded-xl bg-mentora-primary px-5 py-3 text-sm font-semibold text-white hover:bg-mentora-primary-hover"
                        @click="clearFilters"
                    >
                        Tampilkan Semua Event
                    </button>
                </div>

                <div v-else class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <EventCard v-for="item in events.items" :key="item.id" :event="item" />
                </div>

                <nav
                    v-if="events.last_page > 1"
                    class="mt-9 flex items-center justify-center gap-4"
                    aria-label="Navigasi halaman event"
                >
                    <button
                        type="button"
                        :disabled="events.current_page <= 1"
                        class="rounded-xl border border-mentora-border bg-white px-4 py-2.5 text-sm font-medium text-mentora-navy disabled:cursor-not-allowed disabled:opacity-40"
                        @click="goToPage(events.current_page - 1)"
                    >
                        Sebelumnya
                    </button>
                    <span class="text-sm text-slate-600">{{ events.current_page }} / {{ events.last_page }}</span>
                    <button
                        type="button"
                        :disabled="events.current_page >= events.last_page"
                        class="rounded-xl border border-mentora-border bg-white px-4 py-2.5 text-sm font-medium text-mentora-navy disabled:cursor-not-allowed disabled:opacity-40"
                        @click="goToPage(events.current_page + 1)"
                    >
                        Berikutnya
                    </button>
                </nav>
            </div>
        </section>
    </PublicLayout>
</template>
