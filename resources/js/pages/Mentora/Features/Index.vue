
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    CheckCircle2,
    ClipboardList,
    GraduationCap,
    Search,
    ShieldCheck,
    Sparkles,
} from 'lucide-vue-next';

import PublicLayout from '@/layouts/PublicLayout.vue';

// Daftar fitur yang ditampilkan pada halaman publik.
// Hanya fitur yang sudah selesai dikembangkan yang dapat dibuka.
const features = [
    {
        id: 'scholarship-search',
        title: 'Pencarian Beasiswa',
        description:
            'Temukan informasi beasiswa berdasarkan jenjang pendidikan, lokasi studi, jenis pendanaan, dan bidang studi.',
        audience: 'Untuk semua pengunjung',
        icon: Search,
        available: true,
        href: '/beasiswa',
    },
    {
        id: 'eligibility-checker',
        title: 'Eligibility Checker',
        description:
            'Fitur yang direncanakan untuk membantu Member memeriksa kesesuaian profil dengan persyaratan suatu beasiswa.',
        audience: 'Untuk Member',
        icon: ShieldCheck,
        available: false,
        href: '',
    },
    {
        id: 'smart-recommendation',
        title: 'Smart Recommendation',
        description:
            'Fitur rekomendasi beasiswa yang akan dikembangkan berdasarkan profil pendidikan, minat, dan preferensi Member.',
        audience: 'Untuk Member',
        icon: Sparkles,
        available: false,
        href: '',
    },
    {
        id: 'application-tracker',
        title: 'Application Tracker',
        description:
            'Fitur yang direncanakan untuk membantu Member mencatat tahapan, jadwal, dan perkembangan pendaftaran beasiswa.',
        audience: 'Untuk Member',
        icon: ClipboardList,
        available: false,
        href: '',
    },
];
</script>

<template>
    <PublicLayout>
        <Head title="Fitur MENTORA" />

        <section class="bg-mentora-canvas py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Header halaman -->
                <div class="mx-auto max-w-3xl text-center">
                    <p
                        class="text-sm font-semibold
                               text-mentora-primary"
                    >
                        MENTORA FEATURES
                    </p>

                    <h1
                        class="mt-3 text-3xl font-bold
                               tracking-tight text-mentora-navy
                               md:text-4xl"
                    >
                        Fitur untuk Mendukung
                        Perjalanan Beasiswamu
                    </h1>

                    <p class="mt-5 leading-7 text-slate-600">
                        Jelajahi layanan MENTORA untuk menemukan
                        peluang beasiswa, memahami persyaratan,
                        dan mempersiapkan proses pendaftaran.
                    </p>
                </div>

                <!-- Kartu fitur -->
                <div
                    class="mt-12 grid gap-6
                           md:grid-cols-2"
                >
                    <article
                        v-for="feature in features"
                        :key="feature.id"
                        class="flex h-full flex-col rounded-2xl
                               border border-mentora-border
                               bg-white p-6 shadow-sm
                               transition-shadow
                               hover:shadow-md md:p-8"
                    >
                        <div
                            class="flex flex-wrap items-start
                                   justify-between gap-4"
                        >
                            <!-- Ikon fitur -->
                            <div
                                class="flex h-14 w-14 items-center
                                       justify-center rounded-2xl
                                       bg-indigo-50
                                       text-mentora-primary"
                            >
                                <component
                                    :is="feature.icon"
                                    :size="26"
                                    aria-hidden="true"
                                />
                            </div>

                            <!-- Status ketersediaan -->
                            <span
                                v-if="feature.available"
                                class="inline-flex items-center
                                       gap-1.5 rounded-full
                                       bg-emerald-50 px-3 py-1.5
                                       text-xs font-semibold
                                       text-emerald-700"
                            >
                                <CheckCircle2
                                    :size="14"
                                    aria-hidden="true"
                                />
                                Tersedia
                            </span>

                            <span
                                v-else
                                class="rounded-full bg-amber-50
                                       px-3 py-1.5 text-xs
                                       font-semibold text-amber-700"
                            >
                                Segera Hadir
                            </span>
                        </div>

                        <!-- Informasi fitur -->
                        <h2
                            class="mt-6 text-xl font-bold
                                   text-mentora-navy"
                        >
                            {{ feature.title }}
                        </h2>

                        <p
                            class="mt-3 flex-1 text-sm
                                   leading-7 text-slate-600"
                        >
                            {{ feature.description }}
                        </p>

                        <div
                            class="mt-6 border-t
                                   border-mentora-border pt-5"
                        >
                            <p
                                class="mb-4 flex items-center gap-2
                                       text-xs font-medium
                                       text-slate-500"
                            >
                                <GraduationCap
                                    :size="16"
                                    aria-hidden="true"
                                />
                                {{ feature.audience }}
                            </p>

                            <!-- Fitur yang sudah tersedia -->
                            <Link
                                v-if="feature.available"
                                :href="feature.href"
                                class="inline-flex items-center
                                       justify-center gap-2
                                       rounded-xl bg-mentora-primary
                                       px-5 py-3 text-sm
                                       font-semibold text-white
                                       transition
                                       hover:bg-mentora-primary-hover"
                            >
                                Jelajahi Beasiswa

                                <ArrowRight
                                    :size="17"
                                    aria-hidden="true"
                                />
                            </Link>

                            <!-- Fitur yang belum tersedia -->
                            <p
                                v-else
                                class="text-sm font-medium
                                       text-slate-400"
                            >
                                Fitur sedang dalam pengembangan
                            </p>
                        </div>
                    </article>
                </div>

                <!-- Penjelasan status -->
                <div
                    class="mt-10 rounded-2xl border
                           border-mentora-border bg-white
                           p-6 md:p-8"
                >
                    <h2
                        class="text-lg font-bold
                               text-mentora-navy"
                    >
                        MENTORA Terus Dikembangkan
                    </h2>

                    <p
                        class="mt-3 max-w-4xl text-sm
                               leading-7 text-slate-600"
                    >
                        Pencarian dan informasi detail beasiswa
                        sudah tersedia. Fitur pemeriksaan
                        kelayakan, rekomendasi personal, dan
                        pemantauan pendaftaran akan diperkenalkan
                        secara bertahap setelah pengembangan
                        dan pengujiannya selesai.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
