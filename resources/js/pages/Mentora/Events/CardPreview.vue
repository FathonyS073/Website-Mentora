<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import EventCard from '@/components/mentora/EventCard.vue';

// Hanya data demonstrasi UI. Tidak disimpan ke PostgreSQL.
// Waktu memakai ISO UTC agar format WIB pada kartu bisa diuji.
const samples = [
    {
        id: -1,
        title: '[CONTOH] Webinar Strategi Mencari Beasiswa',
        slug: 'preview-event-webinar',
        organizer_name: 'Penyelenggara Contoh A',
        summary: 'Contoh tampilan kartu webinar untuk mengevaluasi tipografi, jadwal, informasi, dan tombol.',
        event_type: 'webinar',
        delivery_mode: 'online',
        location_name: null,
        starts_at: '2027-01-15T02:00:00.000Z',
    },
    {
        id: -2,
        title: '[CONTOH] Workshop Persiapan Dokumen Beasiswa',
        slug: 'preview-event-workshop',
        organizer_name: 'Penyelenggara Contoh B',
        summary: 'Data contoh untuk memeriksa tampilan event luring, informasi lokasi, dan berbagai panjang judul.',
        event_type: 'workshop',
        delivery_mode: 'offline',
        location_name: 'Lokasi contoh, Indonesia',
        starts_at: '2027-01-22T06:00:00.000Z',
    },
    {
        id: -3,
        title: '[CONTOH] Seminar Pendidikan dan Peluang Studi',
        slug: 'preview-event-seminar',
        organizer_name: 'Penyelenggara Contoh C',
        summary: 'Contoh kartu event hybrid. Kegiatan ini bukan pengumuman event resmi atau jadwal nyata.',
        event_type: 'seminar',
        delivery_mode: 'hybrid',
        location_name: 'Lokasi demonstrasi',
        starts_at: '2027-02-05T01:30:00.000Z',
    },
];
</script>

<template>
    <PublicLayout>
        <Head title="Preview Kartu Event - MENTORA" />
        <section class="bg-mentora-canvas py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div role="note" class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-900">
                    PREVIEW LOKAL — Seluruh event di halaman ini merupakan data contoh antarmuka, bukan kegiatan nyata.
                </div>
                <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-mentora-primary">MENTORA UI PREVIEW</p>
                        <h1 class="mt-2 text-2xl font-bold text-mentora-navy sm:text-3xl">Preview Kartu Event</h1>
                    </div>
                    <Link href="/event" class="rounded-xl border border-mentora-border bg-white px-4 py-2.5 text-sm font-semibold text-mentora-primary hover:bg-indigo-50">
                        Kembali ke Katalog Event
                    </Link>
                </div>
                <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <EventCard v-for="item in samples" :key="item.id" :event="item" :preview="true" />
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
