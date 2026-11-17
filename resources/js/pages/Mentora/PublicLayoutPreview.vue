<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import MentoraButton from '@/components/mentora/MentoraButton.vue';
import MentoraCard from '@/components/mentora/MentoraCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { register } from '@/routes';

const presses = ref(0);
</script>

<template>
    <Head title="Pratinjau Public Layout - MENTORA" />

    <PublicLayout>
        <section class="mx-auto grid max-w-7xl gap-10 px-5 py-16 md:px-8 lg:grid-cols-2 lg:items-center lg:py-24">
            <div>
                <span class="inline-flex rounded-full bg-mentora-lavender px-4 py-1.5 text-xs font-semibold text-mentora-primary">
                    Pratinjau internal · S0-06
                </span>
                <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight text-mentora-navy sm:text-5xl">
                    Temukan Beasiswa, Kembangkan Potensi,
                    <span class="text-mentora-primary"> Raih Masa Depanmu.</span>
                </h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-mentora-muted">
                    Fondasi layout publik MENTORA sedang diuji. Konten dan aset visual Homepage
                    final akan diimplementasikan terpisah sesuai desain yang sudah disetujui.
                </p>
                <Link
                    :href="register()"
                    class="mt-7 inline-flex min-h-11 items-center rounded-xl bg-mentora-primary px-6 py-3 text-sm font-semibold text-white hover:bg-mentora-primary-hover"
                >
                    Mulai Sekarang Gratis
                </Link>
            </div>

            <div class="flex min-h-72 items-center justify-center rounded-[2rem] border border-mentora-border bg-gradient-to-br from-mentora-lavender via-white to-mentora-canvas p-7 text-center shadow-mentora-card">
                <div>
                    <div class="mx-auto mb-5 flex size-16 items-center justify-center rounded-2xl bg-white text-3xl font-bold text-mentora-primary shadow-sm">
                        M
                    </div>
                    <p class="font-bold text-mentora-navy">Area ilustrasi Homepage</p>
                    <p class="mt-2 max-w-xs text-sm text-mentora-muted">
                        Placeholder untuk pengujian layout; bukan desain visual final.
                    </p>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 pb-20 md:px-8" aria-labelledby="preview-components-title">
            <h2 id="preview-components-title" class="mb-6 text-2xl font-bold">Pengujian komponen</h2>
            <MentoraCard>
                <p class="font-semibold text-mentora-navy">MentoraCard dan MentoraButton</p>
                <p class="mt-2 text-sm text-mentora-muted">
                    Kedua komponen ini sekarang dirender dalam halaman Vue, sehingga dapat diuji melalui browser.
                </p>
                <div class="mt-5 flex flex-wrap items-center gap-4">
                    <MentoraButton @click="presses++">Uji tombol</MentoraButton>
                    <span class="text-sm text-mentora-muted" aria-live="polite">Diklik {{ presses }} kali</span>
                </div>
            </MentoraCard>
        </section>
    </PublicLayout>
</template>
