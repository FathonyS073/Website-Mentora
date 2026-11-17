<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowRight,
    BookOpen,
    Check,
    ChevronDown,
    ClipboardCheck,
    FileText,
    GraduationCap,
    MapPin,
    MonitorPlay,
    Search,
    ShieldCheck,
    Sparkles,
    Target,
    UserRound,
} from 'lucide-vue-next';
import MentoraButton from '@/components/mentora/MentoraButton.vue';
import MentoraCard from '@/components/mentora/MentoraCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { dashboard, register } from '@/routes';

const page = usePage();
const startHref = computed(() => (page.props.auth.user ? dashboard() : register()));

const keyword = ref('');
const level = ref('');
const location = ref('');
const field = ref('');
const searchNotice = ref('');
const activeFaq = ref<number | null>(0);

const quickSearches = ['Beasiswa S1', 'Beasiswa S2', 'Beasiswa Luar Negeri', 'Fully Funded'];

const features = [
    {
        title: 'Katalog Beasiswa',
        description: 'Jelajahi program beasiswa dari berbagai penyelenggara dengan informasi yang dapat ditelusuri.',
        icon: BookOpen,
        soon: false,
    },
    {
        title: 'Eligibility Checker',
        description: 'Periksa kesesuaian profil dengan persyaratan beasiswa secara jelas dan terstruktur.',
        icon: ShieldCheck,
        soon: false,
    },
    {
        title: 'Smart Recommendation',
        description: 'Rekomendasi relevan berdasarkan profil, jenjang, dan minat studi.',
        icon: Sparkles,
        soon: false,
    },
    {
        title: 'Application Tracker',
        description: 'Kelola progres persiapan, daftar dokumen, dan tenggat waktu dalam satu tempat.',
        icon: ClipboardCheck,
        soon: false,
    },
    {
        title: 'Tryout & Webinar',
        description: 'Latihan soal serta sesi belajar untuk mempersiapkan diri menghadapi seleksi.',
        icon: MonitorPlay,
        soon: true,
    },
    {
        title: 'AI Essay Reviewer',
        description: 'Dapatkan umpan balik yang terstruktur untuk memperbaiki tulisan esai.',
        icon: FileText,
        soon: true,
    },
] as const;

const benefits = [
    { title: 'Informasi yang Dapat Ditelusuri', description: 'Sumber program ditampilkan agar informasi dapat dicek kembali.' },
    { title: 'Personal & Relevan', description: 'Rekomendasi disesuaikan dengan profil dan tujuan studimu.' },
    { title: 'Persiapan Lebih Terarah', description: 'Kelola dokumen, aktivitas, dan persyaratan yang perlu dipenuhi.' },
    { title: 'Mudah Digunakan', description: 'Antarmuka yang sederhana untuk pelajar maupun mahasiswa.' },
];

const steps = [
    { title: 'Buat Akun', description: 'Daftar menggunakan email untuk memulai perjalananmu.', icon: UserRound },
    { title: 'Lengkapi Profil', description: 'Isi jenjang, minat studi, dan preferensi pendidikan.', icon: ClipboardCheck },
    { title: 'Temukan Peluang', description: 'Jelajahi program dan periksa persyaratan yang sesuai.', icon: Search },
    { title: 'Persiapkan Pendaftaran', description: 'Atur dokumen dan pantau langkah persiapanmu.', icon: Target },
];

const preparations = [
    { title: 'Tryout & Assessment', description: 'Latihan dan evaluasi untuk memahami kesiapan menghadapi seleksi.', icon: ClipboardCheck },
    { title: 'Webinar & Learning Events', description: 'Sesi belajar, kelas persiapan, dan diskusi bersama narasumber.', icon: MonitorPlay },
    { title: 'AI Essay Reviewer', description: 'Evaluasi dan saran perbaikan esai berbasis rubrik yang transparan.', icon: FileText },
];

const faqs = [
    {
        question: 'Apakah MENTORA gratis?',
        answer: 'MENTORA dirancang menyediakan akses informasi beasiswa publik tanpa biaya. Jika kelak ada layanan tambahan berbayar, informasinya akan ditampilkan secara jelas sebelum digunakan.',
    },
    {
        question: 'Apakah semua jenjang pendidikan tersedia?',
        answer: 'Cakupan yang direncanakan meliputi SD/MI hingga S3. Jumlah program yang benar-benar tersedia akan bergantung pada data beasiswa yang telah diverifikasi dan diterbitkan.',
    },
    {
        question: 'Apakah harus mendaftar untuk mencari beasiswa?',
        answer: 'Katalog publik direncanakan dapat dilihat tanpa akun. Akun diperlukan untuk menyimpan beasiswa, mempersonalisasi rekomendasi, dan menggunakan tracker.',
    },
    {
        question: 'Apakah MENTORA menjamin saya mendapatkan beasiswa?',
        answer: 'Tidak. Hasil pemeriksaan eligibility dan rekomendasi membantu memahami kecocokan persyaratan, bukan jaminan diterima atau peluang kelulusan seleksi.',
    },
    {
        question: 'Bagaimana data pribadi saya dikelola?',
        answer: 'MENTORA sedang menerapkan pengaturan keamanan dan privasi secara bertahap. Informasi tentang pemrosesan data dan hak pengguna akan disediakan sebelum peluncuran publik.',
    },
];

function runSearch(): void {
    searchNotice.value = 'Pencarian katalog beasiswa belum tersedia pada Sprint 1 ini. Formulir ini masih merupakan pratinjau antarmuka; belum ada pencarian data yang dilakukan.';
}

function applyQuickSearch(term: string): void {
    keyword.value = term;
    searchNotice.value = '';
}

function toggleFaq(index: number): void {
    activeFaq.value = activeFaq.value === index ? null : index;
}
</script>

<template>
    <Head title="Temukan Beasiswa dan Raih Masa Depanmu | MENTORA">
        <meta
            name="description"
            content="MENTORA membantu pelajar dan mahasiswa menjelajahi informasi beasiswa, memahami persyaratan, dan mempersiapkan pendaftaran dari SD/MI hingga S3."
        />
    </Head>

    <PublicLayout>
        <!-- Hero: visual mengikuti LP EdTech MENTORA Modern, bukan Starter Kit Laravel. -->
        <section class="relative overflow-hidden bg-gradient-to-b from-mentora-canvas via-[#f7f8ff] to-white">
            <div class="pointer-events-none absolute -right-24 top-12 hidden size-[34rem] rounded-full bg-mentora-lavender/70 blur-3xl lg:block" aria-hidden="true" />
            <div class="relative mx-auto grid w-full max-w-7xl items-center gap-8 px-5 pb-12 pt-10 md:px-8 lg:grid-cols-[0.92fr_1.08fr] lg:gap-4 lg:pb-8 lg:pt-16 xl:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)] xl:gap-6 xl:pb-4 xl:pt-12">
                <div class="relative z-10 max-w-2xl">
                    <span class="inline-flex items-center rounded-full bg-mentora-lavender px-4 py-2 text-xs font-semibold text-mentora-primary xl:py-1.5">
                        Platform Pendamping Studi &amp; Beasiswa
                    </span>
                    <h1 class="mt-5 text-[clamp(2.45rem,5vw,4.3rem)] font-extrabold leading-[1.09] tracking-[-0.04em] text-mentora-navy xl:mt-4 xl:text-[3.1rem] xl:leading-[1.08] xl:tracking-[-0.035em]">
                        Temukan Beasiswa,<br class="hidden xl:block" /> Kembangkan Potensi,<br />
                        <span class="text-mentora-primary">Raih Masa Depanmu.</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-mentora-muted sm:text-lg xl:mt-4 xl:max-w-[560px] xl:text-base xl:leading-6">
                        MENTORA membantu pelajar dan mahasiswa dari SD/MI hingga S3 menemukan peluang beasiswa,
                        memahami persyaratan, mempersiapkan seleksi, dan mengelola pendaftaran.
                    </p>
                    <div class="mt-7 flex flex-wrap items-center gap-3 xl:mt-5">
                        <Link :href="startHref" class="inline-flex min-h-12 items-center gap-2 rounded-xl bg-mentora-primary px-6 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-500/15 transition hover:bg-mentora-primary-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary">
                            Mulai Sekarang Gratis <ArrowRight :size="17" aria-hidden="true" />
                        </Link>
                        <a href="#peluang-beasiswa" class="inline-flex min-h-12 items-center rounded-xl border border-mentora-primary px-6 py-3 text-sm font-semibold text-mentora-primary transition hover:bg-mentora-lavender focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary">
                            Jelajahi Beasiswa
                        </a>
                    </div>
                </div>
                <div class="relative mx-auto w-full max-w-2xl lg:self-end xl:max-w-none xl:translate-x-3" aria-label="Ilustrasi pelajar dan mahasiswa MENTORA">
                    <div class="pointer-events-none absolute inset-x-8 top-8 h-3/4 rounded-[48%] bg-gradient-to-br from-[#e6e9ff] to-[#f1f3ff] xl:hidden" aria-hidden="true" />
                    <img
                        src="/Images/Hero-Section.webp"
                        alt="Pelajar dan mahasiswa yang membawa perlengkapan studi"
                        class="relative mx-auto block max-h-[440px] w-full object-contain object-bottom xl:max-h-[420px] xl:w-[108%] xl:max-w-none"
                        loading="eager"
                        fetchpriority="high"
                        decoding="async"
                    />
                </div>
            </div>
        </section>

        <!-- Scholarship search: presentational until the Scholarship module exposes public data. -->
        <section class="relative z-10 mx-auto -mt-2 w-full max-w-7xl px-5 md:px-8" aria-labelledby="scholarship-search-title">
            <MentoraCard class="!rounded-[1.45rem] !p-6 shadow-[0_18px_50px_rgba(33,42,118,0.08)] sm:!p-8">
                <h2 id="scholarship-search-title" class="text-2xl font-extrabold tracking-tight text-mentora-navy sm:text-3xl">Cari Beasiswa Impianmu</h2>
                <p class="mt-1 text-sm leading-6 text-mentora-muted">Jelajahi peluang pendidikan dari berbagai penyelenggara di Indonesia dan luar negeri.</p>
                <form class="mt-5 grid gap-3 lg:grid-cols-[minmax(0,2.4fr)_repeat(3,minmax(0,1fr))_auto]" role="search" @submit.prevent="runSearch">
                    <label class="relative block min-w-0">
                        <span class="sr-only">Kata kunci beasiswa</span>
                        <Search :size="18" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-mentora-muted" aria-hidden="true" />
                        <input v-model="keyword" type="search" name="q" placeholder="Nama beasiswa, penyelenggara, atau bidang studi..." class="h-12 w-full rounded-xl border border-mentora-border bg-white pl-11 pr-3 text-sm text-mentora-navy outline-none transition placeholder:text-slate-400 focus:border-mentora-secondary focus:ring-2 focus:ring-mentora-lavender" />
                    </label>
                    <label class="block">
                        <span class="sr-only">Jenjang pendidikan</span>
                        <select v-model="level" name="level" class="h-12 w-full rounded-xl border border-mentora-border bg-white px-3 text-sm text-mentora-navy focus:border-mentora-secondary focus:outline-none focus:ring-2 focus:ring-mentora-lavender">
                            <option value="">Jenjang</option>
                            <option value="SD/MI">SD/MI</option><option value="SMP/MTs">SMP/MTs</option><option value="SMA/SMK/MA">SMA/SMK/MA</option><option value="D3/D4">D3/D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="sr-only">Lokasi studi</span>
                        <select v-model="location" name="location" class="h-12 w-full rounded-xl border border-mentora-border bg-white px-3 text-sm text-mentora-navy focus:border-mentora-secondary focus:outline-none focus:ring-2 focus:ring-mentora-lavender">
                            <option value="">Lokasi</option><option value="dalam-negeri">Dalam Negeri</option><option value="luar-negeri">Luar Negeri</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="sr-only">Bidang studi</span>
                        <select v-model="field" name="field" class="h-12 w-full rounded-xl border border-mentora-border bg-white px-3 text-sm text-mentora-navy focus:border-mentora-secondary focus:outline-none focus:ring-2 focus:ring-mentora-lavender">
                            <option value="">Bidang Studi</option><option value="sains-teknologi">Sains &amp; Teknologi</option><option value="sosial-humaniora">Sosial &amp; Humaniora</option><option value="kesehatan">Kesehatan</option><option value="semua">Lainnya</option>
                        </select>
                    </label>
                    <MentoraButton type="submit" class="min-h-12 whitespace-nowrap px-5"><Search :size="16" aria-hidden="true" /> Cari Beasiswa</MentoraButton>
                </form>
                <p v-if="searchNotice" class="mt-4 rounded-xl border border-mentora-border bg-mentora-lavender/60 px-4 py-3 text-sm text-mentora-navy" role="status" aria-live="polite">{{ searchNotice }}</p>
                <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                    <span class="mr-1 font-semibold text-mentora-navy">Pencarian cepat:</span>
                    <button v-for="item in quickSearches" :key="item" type="button" class="rounded-full bg-mentora-lavender px-3 py-1.5 font-medium text-mentora-primary transition hover:bg-indigo-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary" @click="applyQuickSearch(item)">{{ item }}</button>
                </div>
            </MentoraCard>
        </section>

        <!-- Value proposition -->
        <section class="mx-auto max-w-7xl px-5 py-20 md:px-8" aria-labelledby="features-title">
            <div class="mx-auto max-w-3xl text-center">
                <h2 id="features-title" class="text-3xl font-extrabold tracking-tight text-mentora-navy md:text-4xl">Satu Platform untuk Perjalanan Beasiswamu</h2>
                <p class="mt-3 text-sm leading-7 text-mentora-muted sm:text-base">MENTORA hadir untuk membantumu di setiap tahap, mulai dari mencari peluang hingga mempersiapkan pendaftaran beasiswa.</p>
            </div>
            <div class="mt-12 grid gap-x-9 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="feature in features" :key="feature.title" class="flex items-start gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-mentora-lavender text-mentora-primary"><component :is="feature.icon" :size="24" aria-hidden="true" /></div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2"><h3 class="font-bold text-mentora-navy">{{ feature.title }}</h3><span v-if="feature.soon" class="rounded-full bg-mentora-lavender px-2 py-0.5 text-[10px] font-semibold text-mentora-primary">Segera Hadir</span></div>
                        <p class="mt-1.5 text-sm leading-6 text-mentora-muted">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Beyond search -->
        <section class="bg-gradient-to-b from-white via-mentora-canvas/75 to-white py-16" aria-labelledby="why-title">
            <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 md:px-8 lg:grid-cols-[0.8fr_1.25fr_0.9fr]">
                <div>
                    <h2 id="why-title" class="text-3xl font-extrabold tracking-tight text-mentora-navy">Lebih dari Sekadar<br />Mencari Beasiswa</h2>
                    <p class="mt-4 text-sm leading-7 text-mentora-muted">MENTORA bukan hanya tempat menemukan informasi beasiswa, tetapi pendamping untuk membantumu mempersiapkan langkah pendidikan secara lebih terarah dan percaya diri.</p>
                </div>
                <div class="rounded-[1.5rem] border border-indigo-100 bg-white p-4 shadow-[0_14px_34px_rgba(53,55,151,0.12)]" aria-label="Pratinjau konsep antarmuka dashboard MENTORA">
                    <img
                        src="/Images/Dashboard-Preview.webp"
                        alt="Pratinjau konsep antarmuka dashboard MENTORA"
                        class="mx-auto block w-full rounded-[1.25rem] object-contain object-top"
                        loading="lazy"
                        decoding="async"
                    />
                </div>
                <div class="space-y-6">
                    <div v-for="benefit in benefits" :key="benefit.title" class="flex gap-3"><div class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><Check :size="15" stroke-width="3" aria-hidden="true" /></div><div><h3 class="text-sm font-bold text-mentora-navy">{{ benefit.title }}</h3><p class="mt-1 text-xs leading-5 text-mentora-muted">{{ benefit.description }}</p></div></div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="mx-auto max-w-7xl px-5 py-20 md:px-8" aria-labelledby="how-title">
            <div class="text-center"><h2 id="how-title" class="text-3xl font-extrabold tracking-tight text-mentora-navy md:text-4xl">Bagaimana Cara Kerja MENTORA?</h2><p class="mt-3 text-sm leading-7 text-mentora-muted">Mulai perjalanan beasiswamu dalam beberapa langkah yang mudah dan praktis.</p></div>
            <div class="mt-11 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="(step, index) in steps" :key="step.title" class="relative rounded-2xl border border-mentora-border bg-white p-5 shadow-mentora-card">
                    <div class="absolute -left-2 -top-2 flex size-8 items-center justify-center rounded-full bg-mentora-secondary text-sm font-bold text-white shadow-md">{{ index + 1 }}</div>
                    <div class="mb-5 flex size-12 items-center justify-center rounded-2xl bg-mentora-lavender text-mentora-primary"><component :is="step.icon" :size="24" aria-hidden="true" /></div>
                    <h3 class="text-base font-bold text-mentora-navy">{{ step.title }}</h3><p class="mt-2 text-sm leading-6 text-mentora-muted">{{ step.description }}</p>
                    <ArrowRight v-if="index < steps.length - 1" class="absolute -right-3 top-1/2 z-10 hidden -translate-y-1/2 rounded-full bg-white text-mentora-secondary lg:block" :size="22" aria-hidden="true" />
                </div>
            </div>
        </section>

        <!-- Real-data-first empty state: never fabricate published programs. -->
        <section id="peluang-beasiswa" class="mx-auto max-w-7xl scroll-mt-24 px-5 pb-20 md:px-8" aria-labelledby="opportunities-title">
            <div class="flex flex-wrap items-end justify-between gap-4"><div><h2 id="opportunities-title" class="text-3xl font-extrabold tracking-tight text-mentora-navy">Temukan Peluang Beasiswa untukmu</h2><p class="mt-2 text-sm text-mentora-muted">Program beasiswa akan ditampilkan setelah sumber dan informasinya diverifikasi.</p></div><span class="rounded-full bg-mentora-lavender px-4 py-2 text-xs font-semibold text-mentora-primary">Katalog dalam persiapan</span></div>
            <div class="mt-7 grid gap-4 md:grid-cols-3">
                <MentoraCard class="text-left"><div class="mb-4 inline-flex size-12 items-center justify-center rounded-xl bg-mentora-lavender text-mentora-primary"><GraduationCap :size="24" aria-hidden="true" /></div><h3 class="font-bold text-mentora-navy">Berbagai jenjang studi</h3><p class="mt-2 text-sm leading-6 text-mentora-muted">Cakupan yang direncanakan mulai SD/MI, SMP/MTs, SMA/MA/SMK hingga pendidikan tinggi.</p></MentoraCard>
                <MentoraCard class="text-left"><div class="mb-4 inline-flex size-12 items-center justify-center rounded-xl bg-mentora-lavender text-mentora-primary"><MapPin :size="24" aria-hidden="true" /></div><h3 class="font-bold text-mentora-navy">Dalam dan luar negeri</h3><p class="mt-2 text-sm leading-6 text-mentora-muted">Pilihan lokasi studi akan mengikuti ketersediaan program dengan sumber informasi yang jelas.</p></MentoraCard>
                <MentoraCard class="text-left"><div class="mb-4 inline-flex size-12 items-center justify-center rounded-xl bg-mentora-lavender text-mentora-primary"><ShieldCheck :size="24" aria-hidden="true" /></div><h3 class="font-bold text-mentora-navy">Informasi terverifikasi</h3><p class="mt-2 text-sm leading-6 text-mentora-muted">Sumber, persyaratan, dan batas pendaftaran akan ditinjau sebelum program dipublikasikan.</p></MentoraCard>
            </div>
            <p class="mt-5 text-center text-xs text-mentora-muted">Belum ada program beasiswa yang dipublikasikan pada versi development ini.</p>
        </section>

        <!-- Preparation features -->
        <section class="bg-mentora-canvas/70 py-20" aria-labelledby="prepare-title">
            <div class="mx-auto max-w-7xl px-5 md:px-8"><div class="text-center"><h2 id="prepare-title" class="text-3xl font-extrabold tracking-tight text-mentora-navy">Persiapkan Dirimu untuk Setiap Peluang</h2><p class="mt-3 text-sm text-mentora-muted">Berbagai fitur persiapan yang membantu proses pendaftaran beasiswa lebih matang.</p></div>
                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <MentoraCard v-for="item in preparations" :key="item.title" class="flex h-full flex-col"><div class="mb-5 flex size-12 items-center justify-center rounded-2xl bg-mentora-lavender text-mentora-primary"><component :is="item.icon" :size="24" aria-hidden="true" /></div><div class="flex flex-wrap items-center gap-2"><h3 class="font-bold text-mentora-navy">{{ item.title }}</h3><span class="rounded-full bg-mentora-lavender px-2 py-0.5 text-[10px] font-semibold text-mentora-primary">Segera Hadir</span></div><p class="mt-2 flex-1 text-sm leading-6 text-mentora-muted">{{ item.description }}</p><span class="mt-6 inline-flex w-fit rounded-lg border border-mentora-border px-4 py-2 text-xs font-semibold text-mentora-muted">Sedang disiapkan</span></MentoraCard>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="mx-auto max-w-4xl px-5 py-20 md:px-8" aria-labelledby="faq-title">
            <h2 id="faq-title" class="text-center text-3xl font-extrabold tracking-tight text-mentora-navy">Pertanyaan yang Sering Diajukan</h2><p class="mt-3 text-center text-sm text-mentora-muted">Temukan jawaban untuk pertanyaan umum seputar MENTORA.</p>
            <div class="mt-9 space-y-3">
                <div v-for="(faq, index) in faqs" :key="faq.question" class="overflow-hidden rounded-xl border border-mentora-border bg-white">
                    <h3><button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-sm font-bold text-mentora-navy hover:bg-mentora-canvas focus-visible:outline-2 focus-visible:outline-mentora-primary" :aria-expanded="activeFaq === index" :aria-controls="`faq-answer-${index}`" @click="toggleFaq(index)">{{ faq.question }}<ChevronDown :size="18" class="shrink-0 text-mentora-primary transition-transform" :class="activeFaq === index ? 'rotate-180' : ''" aria-hidden="true" /></button></h3>
                    <div v-if="activeFaq === index" :id="`faq-answer-${index}`" class="border-t border-mentora-border bg-mentora-lavender/40 px-5 py-4 text-sm leading-6 text-mentora-muted">{{ faq.answer }}</div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="mx-auto max-w-7xl px-5 pb-20 md:px-8" aria-labelledby="cta-title">
            <div class="relative overflow-hidden rounded-[1.5rem] bg-gradient-to-r from-mentora-primary via-[#4652d4] to-[#6976ea] px-6 py-10 text-white sm:px-10 lg:flex lg:items-center lg:justify-between lg:gap-8">
                <div class="pointer-events-none absolute -right-20 -top-32 size-96 rounded-full border-[70px] border-white/10" aria-hidden="true" />
                <div class="relative"><h2 id="cta-title" class="max-w-xl text-2xl font-extrabold tracking-tight sm:text-3xl">Langkah Besar Dimulai dari<br />Peluang yang Tepat.</h2><p class="mt-3 max-w-xl text-sm leading-6 text-white/85">Mulai jelajahi perjalanan pendidikanmu bersama MENTORA dan persiapkan masa depan dengan lebih terarah.</p></div>
                <Link :href="startHref" class="relative mt-6 inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-mentora-primary transition hover:bg-mentora-lavender focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white lg:mt-0">Mulai Gratis <ArrowRight :size="17" aria-hidden="true" /></Link>
            </div>
        </section>
    </PublicLayout>
</template>
