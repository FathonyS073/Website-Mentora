
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

// Data dikirim oleh ScholarshipDetailController.
// Halaman ini merupakan fondasi sebelum desain final S1-03.3.
interface Scholarship {
    id: number;
    title: string;
    slug: string;
    provider_name: string;
    summary: string | null;
    description: string | null;
    education_levels: string[];
    location_scope: string;
    funding_type: string;
}

defineProps<{
    scholarship: Scholarship;
}>();
</script>

<template>
    <PublicLayout>
        <Head :title="`${scholarship.title} - Beasiswa MENTORA`" />

        <section class="bg-mentora-canvas py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-5 md:px-8">

                <!-- Navigasi kembali ke katalog -->
                <Link
                    href="/beasiswa"
                    class="text-sm font-semibold text-mentora-primary"
                >
                    ← Kembali ke Katalog Beasiswa
                </Link>

                <!-- Identitas program beasiswa -->
                <div
                    class="mt-6 rounded-2xl border border-mentora-border
                           bg-white p-6 shadow-sm md:p-8"
                >
                    <p class="text-sm text-mentora-primary">
                        {{ scholarship.provider_name }}
                    </p>

                    <h1
                        class="mt-2 text-3xl font-bold
                               text-mentora-navy"
                    >
                        {{ scholarship.title }}
                    </h1>

                    <p class="mt-4 text-slate-600">
                        {{ scholarship.summary || 'Ringkasan belum tersedia.' }}
                    </p>

                    <!-- Informasi dasar -->
                    <div class="mt-6 flex flex-wrap gap-3 text-sm">
                        <span class="rounded-lg bg-mentora-lavender px-3 py-2">
                            {{ scholarship.education_levels.join(', ') }}
                        </span>

                        <span class="rounded-lg bg-mentora-lavender px-3 py-2">
                            {{ scholarship.location_scope }}
                        </span>

                        <span class="rounded-lg bg-mentora-lavender px-3 py-2">
                            {{ scholarship.funding_type }}
                        </span>
                    </div>
                </div>

                <!-- Deskripsi lengkap program -->
                <div
                    class="mt-6 rounded-2xl border border-mentora-border
                           bg-white p-6 shadow-sm md:p-8"
                >
                    <h2 class="text-xl font-bold text-mentora-navy">
                        Tentang Program Beasiswa
                    </h2>

                    <p class="mt-4 whitespace-pre-line text-slate-600">
                        {{ scholarship.description || 'Deskripsi belum tersedia.' }}
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
