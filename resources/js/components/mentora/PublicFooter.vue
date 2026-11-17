<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import MentoraBrand from './MentoraBrand.vue';

const year = new Date().getFullYear();

// These are intentionally labels, not links to routes that do not exist yet.
const platformItems = ['Beasiswa', 'Fitur', 'Event'];
const informationItems = ['Artikel', 'Tentang Kami', 'FAQ'];
const legalItems = ['Kebijakan Privasi', 'Syarat & Ketentuan', 'Kontak'];
</script>

<template>
    <footer class="border-t border-mentora-border bg-white text-mentora-navy">
        <div class="mx-auto grid max-w-7xl gap-10 px-5 py-12 md:grid-cols-2 md:px-8 lg:grid-cols-4">
            <div class="space-y-4">
                <Link href="/" aria-label="MENTORA - Beranda">
                    <MentoraBrand />
                </Link>
                <p class="max-w-xs text-sm leading-6 text-mentora-muted">
                    Platform pendamping studi dan beasiswa yang membantu pelajar dan mahasiswa
                    menemukan peluang serta mempersiapkan pendaftaran.
                </p>
            </div>

            <div>
                <h2 class="mb-4 text-sm font-bold">Platform</h2>
                <ul class="space-y-3 text-sm">
                    <li><Link href="/" class="text-mentora-muted hover:text-mentora-primary">Beranda</Link></li>
                    <li v-for="item in platformItems" :key="item" class="text-mentora-muted">
                        <span :title="`${item} sedang disiapkan`">{{ item }}</span>
                    </li>
                </ul>
            </div>

            <div>
                <h2 class="mb-4 text-sm font-bold">Informasi</h2>
                <ul class="space-y-3 text-sm text-mentora-muted">
                    <li v-for="item in informationItems" :key="item">{{ item }}</li>
                </ul>
            </div>

            <div>
                <h2 class="mb-4 text-sm font-bold">Legal &amp; Kontak</h2>
                <ul class="space-y-3 text-sm text-mentora-muted">
                    <li v-for="item in legalItems" :key="item">{{ item }}</li>
                </ul>
            </div>
        </div>

        <div class="mx-auto max-w-7xl border-t border-mentora-border px-5 py-5 text-xs text-mentora-muted md:px-8">
            © {{ year }} MENTORA. Hak cipta dilindungi.
        </div>
    </footer>
</template>
