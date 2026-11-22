<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

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
}

const props = defineProps<{ event: EventItem }>();

function formatDate(value: string | null): string {
    if (!value) return 'Belum diinformasikan';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return 'Belum diinformasikan';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
        hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta',
    }).format(date) + ' WIB';
}

function safeLink(value: string | null): string | null {
    if (!value) return null;
    try {
        const url = new URL(value);
        return ['https:', 'http:'].includes(url.protocol) ? url.toString() : null;
    } catch {
        return null;
    }
}

const sourceUrl = computed(() => safeLink(props.event.source_url));
const registrationUrl = computed(() => safeLink(props.event.registration_url));
</script>

<template>
    <PublicLayout>
        <Head :title="`${event.title} - Event MENTORA`" />
        <!-- Fondasi detail; layout final diperhalus pada S1-04.4F. -->
        <section class="bg-mentora-canvas py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Link href="/event" class="text-sm font-semibold text-mentora-primary hover:underline">← Kembali ke Daftar Event</Link>
                <div class="mt-6 rounded-2xl border border-mentora-border bg-white p-6 shadow-sm md:p-8">
                    <p class="text-sm font-semibold text-mentora-primary">{{ event.organizer_name }}</p>
                    <h1 class="mt-3 text-3xl font-bold text-mentora-navy">{{ event.title }}</h1>
                    <p class="mt-4 text-slate-600">{{ event.summary || 'Ringkasan belum tersedia.' }}</p>
                    <div class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                        <p><span class="font-semibold">Jenis kegiatan:</span> {{ event.event_type }}</p>
                        <p><span class="font-semibold">Metode:</span> {{ event.delivery_mode }}</p>
                        <p><span class="font-semibold">Lokasi:</span> {{ event.location_name || 'Lihat informasi penyelenggara' }}</p>
                        <p><span class="font-semibold">Mulai:</span> {{ formatDate(event.starts_at) }}</p>
                        <p><span class="font-semibold">Selesai:</span> {{ formatDate(event.ends_at) }}</p>
                        <p><span class="font-semibold">Batas pendaftaran:</span> {{ formatDate(event.registration_closes_at) }}</p>
                    </div>
                </div>
                <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <section class="rounded-2xl border border-mentora-border bg-white p-6 shadow-sm md:p-8">
                        <h2 class="text-xl font-bold text-mentora-navy">Tentang Event</h2>
                        <p class="mt-4 whitespace-pre-line break-words leading-7 text-slate-600">{{ event.description || 'Deskripsi lengkap belum tersedia. Periksa sumber resmi.' }}</p>
                    </section>
                    <aside class="rounded-2xl border border-mentora-border bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-mentora-navy">Informasi Resmi</h2>
                        <div class="mt-5 flex flex-col gap-3">
                            <a v-if="registrationUrl" :href="registrationUrl" target="_blank" rel="noopener noreferrer" class="rounded-xl bg-mentora-primary px-4 py-3 text-center text-sm font-semibold text-white">Buka Pendaftaran Resmi</a>
                            <a v-if="sourceUrl" :href="sourceUrl" target="_blank" rel="noopener noreferrer" class="rounded-xl border border-mentora-border px-4 py-3 text-center text-sm font-semibold text-mentora-navy">Lihat Website Penyelenggara</a>
                            <p v-if="!registrationUrl && !sourceUrl" class="text-sm text-slate-500">Tautan resmi belum tersedia.</p>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
