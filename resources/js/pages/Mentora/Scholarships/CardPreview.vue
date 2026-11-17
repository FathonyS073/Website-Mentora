<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import ScholarshipCard from '@/components/mentora/ScholarshipCard.vue';

function dayOffset(offset: number): string {
    const date = new Date();
    date.setDate(date.getDate() + offset);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// DATA CONTOH UNTUK PREVIEW LOKAL SAJA, TIDAK MEMBACA/MENULIS DATABASE.
const samples = [
    {
        id: -1,
        title: '[CONTOH] Program Beasiswa Sarjana Nusantara',
        slug: 'contoh-sarjana',
        provider_name: 'Penyelenggara Contoh A',
        summary: 'Contoh susunan kartu program untuk menguji keterbacaan, badge, dan responsivitas.',
        education_levels: ['S1', 'D4'],
        funding_type: 'fully_funded',
        location_scope: 'indonesia',
        opens_at: dayOffset(-10),
        closes_at: dayOffset(30),
        source_url: null,
    },
    {
        id: -2,
        title: '[CONTOH] Program Studi Luar Negeri',
        slug: 'contoh-internasional',
        provider_name: 'Penyelenggara Contoh B',
        summary: 'Kartu contoh untuk status pendaftaran yang belum dibuka dan cakupan luar negeri.',
        education_levels: ['S2', 'S3'],
        funding_type: 'partially_funded',
        location_scope: 'international',
        opens_at: dayOffset(14),
        closes_at: dayOffset(75),
        source_url: null,
    },
    {
        id: -3,
        title: '[CONTOH] Program Beasiswa yang Sudah Ditutup',
        slug: 'contoh-ditutup',
        provider_name: 'Penyelenggara Contoh C',
        summary: 'Kartu contoh untuk melihat tampilan status pendaftaran ditutup.',
        education_levels: ['SMA/MA/SMK'],
        funding_type: 'fully_funded',
        location_scope: 'indonesia',
        opens_at: dayOffset(-60),
        closes_at: dayOffset(-5),
        source_url: null,
    },
];
</script>

<template>
    <PublicLayout>
        <Head title="Preview Lokal — Kartu Beasiswa" />
        <section class="min-h-[70vh] bg-mentora-canvas py-10">
            <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900" role="status">
                    PREVIEW LOKAL — Seluruh program di bawah adalah data contoh UI, bukan beasiswa sebenarnya.
                </div>
                <h1 class="text-2xl font-extrabold text-mentora-navy">Preview Kartu Beasiswa MENTORA</h1>
                <ScholarshipCard v-for="item in samples" :key="item.id" :scholarship="item" />
            </div>
        </section>
    </PublicLayout>
</template>
