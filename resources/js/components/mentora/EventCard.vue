<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    CalendarDays,
    CalendarRange,
    MapPin,
    MonitorPlay,
} from 'lucide-vue-next';

interface EventItem {
    id: number;
    title: string;
    slug: string;
    organizer_name: string;
    summary: string | null;
    event_type: string;
    delivery_mode: string;
    location_name?: string | null;
    starts_at: string;
}

const props = withDefaults(defineProps<{
    event: EventItem;
    preview?: boolean;
}>(), {
    preview: false,
});

const typeLabels: Record<string, string> = {
    webinar: 'Webinar',
    seminar: 'Seminar',
    workshop: 'Workshop',
    other: 'Kegiatan Lainnya',
};

const modeLabels: Record<string, string> = {
    online: 'Online',
    offline: 'Offline',
    hybrid: 'Hybrid',
};

const typeLabel = computed(() =>
    typeLabels[props.event.event_type] ?? props.event.event_type
);
const modeLabel = computed(() =>
    modeLabels[props.event.delivery_mode] ?? props.event.delivery_mode
);

const detailUrl = computed(() =>
    `/event/${encodeURIComponent(props.event.slug)}`
);

function formatStart(value: string): string {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'Jadwal belum tersedia';
    }

    // Semua jadwal pada kartu ditampilkan dalam WIB untuk konsistensi.
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Jakarta',
    }).format(date) + ' WIB';
}
</script>

<template>
    <article
        class="group flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-mentora-border bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
    >
        <!-- Sampul ilustratif netral: tidak mengklaim poster resmi event. -->
        <div class="relative flex min-h-36 items-center justify-center overflow-hidden bg-gradient-to-br from-indigo-50 via-blue-50 to-violet-100 p-5">
            <span class="absolute left-5 top-5 rounded-full border border-indigo-100 bg-white/90 px-3 py-1.5 text-xs font-semibold text-mentora-primary">
                {{ typeLabel }}
            </span>
            <CalendarRange :size="64" :stroke-width="1.35" class="text-mentora-primary/70" aria-hidden="true" />
            <span class="absolute bottom-4 right-5 inline-flex items-center gap-1.5 rounded-full bg-white/90 px-3 py-1.5 text-xs font-medium text-mentora-navy">
                <MonitorPlay :size="14" aria-hidden="true" />
                {{ modeLabel }}
            </span>
        </div>

        <div class="flex flex-1 flex-col p-5 sm:p-6">
            <p class="text-xs font-semibold text-mentora-primary">
                {{ event.organizer_name }}
            </p>

            <h2 class="mt-2 text-lg font-bold leading-snug text-mentora-navy">
                {{ event.title }}
            </h2>

            <p class="mt-3 line-clamp-3 flex-1 text-sm leading-6 text-slate-600">
                {{ event.summary || 'Informasi lengkap kegiatan tersedia pada halaman detail event.' }}
            </p>

            <div class="mt-5 space-y-3 border-t border-mentora-border pt-4 text-sm text-slate-600">
                <div class="flex items-start gap-2">
                    <CalendarDays :size="17" class="mt-0.5 shrink-0 text-mentora-primary" aria-hidden="true" />
                    <span>{{ formatStart(event.starts_at) }}</span>
                </div>
                <div v-if="event.location_name" class="flex items-start gap-2">
                    <MapPin :size="17" class="mt-0.5 shrink-0 text-mentora-primary" aria-hidden="true" />
                    <span class="break-words">{{ event.location_name }}</span>
                </div>
            </div>

            <Link
                v-if="!preview"
                :href="detailUrl"
                :aria-label="`Lihat detail event ${event.title}`"
                class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-mentora-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-mentora-primary-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary"
            >
                Lihat Detail Event
                <ArrowRight :size="17" aria-hidden="true" />
            </Link>
            <div
                v-else
                class="mt-5 rounded-xl border border-dashed border-indigo-200 px-4 py-3 text-center text-xs font-semibold text-mentora-primary"
            >
                Pratinjau desain — bukan event nyata
            </div>
        </div>
    </article>
</template>
