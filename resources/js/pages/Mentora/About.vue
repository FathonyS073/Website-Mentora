<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    ArrowRight,
    BookOpen,
    CalendarCheck2,
    Check,
    CheckCircle2,
    ChevronDown,
    ClipboardCheck,
    Compass,
    Eye,
    Flag,
    GraduationCap,
    Heart,
    Search,
    ShieldCheck,
    Sparkles,
    Target,
    UserRound,
    Users,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
const activeFaq = ref<number | null>(0);

const values = [
    {
        icon: BookOpen,
        title: 'Akses Terbuka',
        description: 'Informasi peluang pendidikan dapat dijelajahi oleh pengunjung tanpa harus membuat akun.',
    },
    {
        icon: UserRound,
        title: 'Personal & Relevan',
        description: 'Pengalaman dirancang untuk mendukung kebutuhan berdasarkan jenjang, minat, dan tujuan belajar.',
    },
    {
        icon: Eye,
        title: 'Transparan',
        description: 'Informasi, sumber, dan persyaratan disajikan dengan jelas agar mudah dipahami dan dibandingkan.',
    },
    {
        icon: Heart,
        title: 'Berorientasi Pengguna',
        description: 'Antarmuka dirancang untuk membantu pelajar, mahasiswa, dan orang tua mengambil keputusan.',
    },
];

const capabilities = [
    {
        icon: Search,
        title: 'Eksplorasi Beasiswa',
        description: 'Temukan program berdasarkan informasi yang tersedia dalam katalog publik.',
        status: 'Tersedia',
    },
    {
        icon: ShieldCheck,
        title: 'Eligibility & Matching',
        description: 'Dirancang untuk membantu membandingkan profil dengan persyaratan program.',
        status: 'Dalam pengembangan',
    },
    {
        icon: ClipboardCheck,
        title: 'Application Tracker',
        description: 'Direncanakan untuk mencatat dokumen, tahapan, dan tenggat pendaftaran.',
        status: 'Dalam pengembangan',
    },
    {
        icon: CalendarCheck2,
        title: 'Event & Artikel Edukasi',
        description: 'Jelajahi informasi kegiatan dan panduan edukatif yang telah dipublikasikan.',
        status: 'Tersedia',
    },
];

const educationLevels = [
    { icon: BookOpen, title: 'SD/MI – SMP/MTs', description: 'Mendukung pendampingan pencarian peluang pendidikan sejak dini.' },
    { icon: GraduationCap, title: 'SMA/SMK/MA', description: 'Membantu siswa menelusuri informasi dan mempersiapkan studi lanjutan.' },
    { icon: Users, title: 'Diploma & S1', description: 'Mendukung eksplorasi peluang studi dan bantuan pendidikan tinggi.' },
    { icon: Target, title: 'S2 & S3', description: 'Mendukung pencarian informasi beasiswa studi lanjut dan riset.' },
];

const faqs = [
    {
        question: 'Apa itu MENTORA?',
        answer: 'MENTORA adalah platform pendamping studi dan informasi beasiswa yang membantu pelajar dan mahasiswa mencari peluang pendidikan serta mempersiapkan proses pendaftaran. Sejumlah fitur pendampingan lanjutan masih dalam tahap pengembangan.',
    },
    {
        question: 'Apakah mencari beasiswa di MENTORA harus membuat akun?',
        answer: 'Tidak. Pengunjung dapat menelusuri katalog dan membaca informasi publik tanpa login. Akun dibutuhkan untuk fitur pribadi tertentu saat fitur tersebut tersedia.',
    },
    {
        question: 'Apakah MENTORA menyediakan atau menjamin beasiswa?',
        answer: 'Tidak. MENTORA merupakan platform informasi dan pendampingan, bukan pemberi beasiswa. Keputusan seleksi, pendanaan, dan jadwal ditentukan oleh masing-masing penyelenggara. Selalu periksa sumber resmi.',
    },
    {
        question: 'Apakah fitur rekomendasi dan tracker sudah dapat digunakan?',
        answer: 'Fitur pemeriksaan kelayakan, rekomendasi personal, dan pemantauan pendaftaran merupakan bagian dari rencana pengembangan. Fitur yang tersedia saat ini ditandai secara terpisah.',
    },
];

function toggleFaq(index: number): void {
    activeFaq.value = activeFaq.value === index ? null : index;
}
</script>

<template>
    <PublicLayout>
        <Head title="Tentang MENTORA - Pendamping Studi dan Beasiswa" />

        <!-- Hero / identitas platform -->
        <section class="overflow-hidden border-b border-indigo-100 bg-gradient-to-br from-indigo-50/90 via-slate-50 to-white">
            <div class="mx-auto max-w-7xl px-5 pb-16 pt-9 sm:px-8 lg:pb-24 lg:pt-12">
                <nav aria-label="Breadcrumb" class="mb-9 flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/" class="hover:text-indigo-700">Beranda</Link>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page" class="font-semibold text-indigo-700">Tentang Kami</span>
                </nav>

                <div class="grid items-center gap-12 lg:grid-cols-[1fr_0.96fr] lg:gap-16">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white px-4 py-2 text-xs font-bold uppercase tracking-wide text-indigo-700 shadow-sm">
                            <Sparkles :size="15" aria-hidden="true" /> Kenali MENTORA
                        </div>
                        <h1 class="mt-6 max-w-2xl text-4xl font-extrabold leading-[1.14] tracking-tight text-slate-900 sm:text-5xl lg:text-[3.55rem]">
                            Membuka Akses <span class="text-indigo-700">Peluang Pendidikan</span> untuk Semua.
                        </h1>
                        <p class="mt-6 max-w-xl text-base leading-8 text-slate-600">
                            Kami percaya setiap pelajar berhak menemukan peluang untuk bertumbuh.
                            MENTORA dirancang untuk membantu perjalanan beasiswa menjadi lebih mudah dipahami, relevan, dan terarah.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <Link href="/beasiswa" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-indigo-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-700">
                                Jelajahi MENTORA
                                <ArrowRight :size="17" aria-hidden="true" />
                            </Link>
                            <a href="#visi-misi" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-indigo-200 bg-white px-6 py-3 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-700">
                                Visi & Misi Kami
                            </a>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-[540px] py-9 lg:py-12" aria-label="Ilustrasi pencarian informasi beasiswa MENTORA">
                        <div class="pointer-events-none absolute inset-5 rounded-full bg-indigo-100 blur-2xl" aria-hidden="true"></div>
                        <div class="pointer-events-none absolute right-1 top-0 h-64 w-64 rounded-full bg-indigo-200/60" aria-hidden="true"></div>
                        <div class="relative mx-auto w-[88%] rotate-[-2deg] rounded-[26px] border border-indigo-100 bg-white p-5 shadow-[0_25px_80px_-20px_rgba(49,46,129,0.23)] sm:p-7">
                            <div class="flex items-center gap-2 border-b border-slate-100 pb-4">
                                <div class="flex size-9 items-center justify-center rounded-xl bg-indigo-700 text-white"><BookOpen :size="20" aria-hidden="true" /></div>
                                <div><p class="text-sm font-extrabold text-slate-900">MENTORA</p><p class="text-[11px] text-slate-500">Eksplorasi peluang studi</p></div>
                            </div>
                            <p class="mt-6 text-xs font-bold uppercase tracking-wider text-indigo-600">Temukan peluangmu</p>
                            <h2 class="mt-1 text-lg font-extrabold text-slate-900 sm:text-xl">Beasiswa yang lebih relevan</h2>
                            <div class="mt-5 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-400">
                                <Search :size="16" aria-hidden="true" /><span>Cari berdasarkan jenjang dan minat studi...</span>
                            </div>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <span v-for="tag in ['S1', 'S2', 'Luar Negeri', 'Dalam Negeri']" :key="tag" class="rounded-full bg-indigo-50 px-3 py-1.5 text-[11px] font-semibold text-indigo-700">{{ tag }}</span>
                            </div>
                            <div class="mt-6 space-y-3">
                                <div class="flex items-center gap-3 rounded-xl border border-slate-100 px-3 py-3">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700"><Compass :size="17" /></span>
                                    <div><p class="text-xs font-bold text-slate-900">Eksplorasi Program</p><p class="text-[11px] text-slate-500">Telusuri berbagai kesempatan</p></div>
                                    <CheckCircle2 class="ml-auto text-emerald-500" :size="17" aria-hidden="true" />
                                </div>
                                <div class="flex items-center gap-3 rounded-xl border border-slate-100 px-3 py-3">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700"><BookOpen :size="17" /></span>
                                    <div><p class="text-xs font-bold text-slate-900">Panduan Persiapan</p><p class="text-[11px] text-slate-500">Pelajari langkah penting</p></div>
                                    <ArrowRight class="ml-auto text-indigo-500" :size="17" aria-hidden="true" />
                                </div>
                            </div>
                        </div>
                        <div class="absolute -left-2 top-4 hidden items-center gap-2 rounded-xl border border-indigo-100 bg-white p-3 shadow-xl sm:flex">
                            <Sparkles :size="18" class="text-indigo-600" aria-hidden="true" />
                            <div><p class="text-[11px] font-bold text-slate-900">Peluang untuk Semua</p><p class="text-[10px] text-slate-500">SD hingga S3</p></div>
                        </div>
                        <div class="absolute -bottom-1 -right-1 flex items-center gap-2 rounded-xl border border-emerald-100 bg-white p-3 shadow-xl sm:-right-4">
                            <ShieldCheck :size="20" class="text-emerald-600" aria-hidden="true" />
                            <div><p class="text-[11px] font-bold text-slate-900">Informasi Transparan</p><p class="text-[10px] text-slate-500">Sumber dapat ditelusuri</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Keunggulan pendek -->
        <section class="border-b border-slate-100 bg-white">
            <div class="mx-auto grid max-w-7xl gap-5 px-5 py-7 sm:px-8 md:grid-cols-3 md:divide-x md:divide-slate-200">
                <div class="flex items-center gap-4 md:px-5" v-for="(value, i) in [
                    { icon: GraduationCap, title: 'Jenjang SD/MI – S3', text: 'Mendukung beragam tahap studi' },
                    { icon: Search, title: 'Pencarian Tanpa Login', text: 'Temukan informasi publik dengan mudah' },
                    { icon: ShieldCheck, title: 'Informasi Dapat Ditelusuri', text: 'Sumber dan detail disajikan transparan' },
                ]" :key="i">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700"><component :is="value.icon" :size="20" /></span>
                    <div><p class="text-sm font-bold text-slate-900">{{ value.title }}</p><p class="mt-1 text-xs text-slate-500">{{ value.text }}</p></div>
                </div>
            </div>
        </section>

        <!-- Latar belakang -->
        <section class="bg-white py-20 lg:py-24">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 sm:px-8 lg:grid-cols-2 lg:gap-20">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[.18em] text-indigo-700">Mengapa MENTORA Ada?</p>
                    <h2 class="mt-4 max-w-lg text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Kesempatan yang tepat seharusnya lebih mudah ditemukan.</h2>
                    <p class="mt-5 text-sm leading-7 text-slate-600">Informasi beasiswa sering tersebar di berbagai tempat. Persyaratan, batas pendaftaran, dan proses persiapan dapat terasa membingungkan.</p>
                    <p class="mt-4 text-sm leading-7 text-slate-600"><strong class="text-slate-900">MENTORA hadir sebagai gagasan untuk menyederhanakan perjalanan itu</strong> — dari menemukan informasi, memahami syarat, hingga mengatur langkah persiapan dalam satu ekosistem digital yang berkembang bertahap.</p>
                </div>
                <div class="rounded-[28px] bg-gradient-to-br from-indigo-50 to-slate-50 p-6 sm:p-9">
                    <p class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-indigo-700"><CheckCircle2 :size="15" /> Satu perjalanan, lebih terarah</p>
                    <div class="mt-8 grid grid-cols-3 gap-2 sm:gap-4">
                        <div v-for="step in [
                            { icon: Search, title: 'Temukan', text: 'Eksplorasi peluang' },
                            { icon: BookOpen, title: 'Pahami', text: 'Pelajari persyaratan' },
                            { icon: ClipboardCheck, title: 'Persiapkan', text: 'Rencanakan langkah' },
                        ]" :key="step.title" class="rounded-2xl bg-white px-2 py-5 text-center shadow-sm sm:px-4">
                            <span class="mx-auto flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700"><component :is="step.icon" :size="21" /></span>
                            <h3 class="mt-3 text-sm font-bold text-slate-900">{{ step.title }}</h3>
                            <p class="mt-1 text-[11px] leading-5 text-slate-500">{{ step.text }}</p>
                        </div>
                    </div>
                    <p class="mt-6 text-center text-xs text-slate-500">Didesain untuk membantu pengguna mengambil langkah berikutnya dengan lebih percaya diri.</p>
                </div>
            </div>
        </section>

        <!-- Nilai-nilai -->
        <section class="bg-slate-50 py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="mx-auto max-w-2xl text-center"><p class="text-xs font-extrabold uppercase tracking-[.18em] text-indigo-700">Prinsip Kami</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Nilai yang Memandu MENTORA</h2><p class="mt-4 text-sm leading-7 text-slate-600">Kami ingin menghadirkan pengalaman mencari beasiswa yang inklusif, jelas, dan benar-benar bermanfaat bagi penggunanya.</p></div>
                <div class="mt-11 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <article v-for="value in values" :key="value.title" class="rounded-2xl border border-indigo-50 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700"><component :is="value.icon" :size="21" /></span>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">{{ value.title }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ value.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Visi misi -->
        <section id="visi-misi" class="scroll-mt-24 bg-white py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="text-center"><p class="text-xs font-extrabold uppercase tracking-[.18em] text-indigo-700">Arah Kami</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Visi & Misi MENTORA</h2></div>
                <div class="mt-10 grid gap-6 lg:grid-cols-2">
                    <article class="relative overflow-hidden rounded-[26px] bg-gradient-to-br from-indigo-800 to-indigo-500 p-8 text-white sm:p-10"><div class="pointer-events-none absolute -right-12 -top-16 size-52 rounded-full border-[27px] border-white/10" aria-hidden="true"></div><Target :size="25" class="relative opacity-90" aria-hidden="true"/><h3 class="relative mt-8 text-2xl font-extrabold">Visi Kami</h3><p class="relative mt-4 max-w-md text-sm leading-8 text-indigo-50">Menjadi platform pendamping pendidikan yang membantu lebih banyak pelajar menemukan peluang belajar dan beasiswa secara lebih inklusif dan terarah.</p></article>
                    <article class="relative overflow-hidden rounded-[26px] bg-indigo-50 p-8 sm:p-10"><div class="pointer-events-none absolute -right-12 -top-16 size-52 rounded-full border-[27px] border-indigo-100" aria-hidden="true"></div><Flag :size="25" class="relative text-indigo-700" aria-hidden="true"/><h3 class="relative mt-8 text-2xl font-extrabold text-slate-900">Misi Kami</h3><p class="relative mt-4 max-w-md text-sm leading-8 text-slate-600">Memudahkan penemuan peluang, membantu pemahaman persyaratan, menyediakan sarana persiapan, dan membangun pengalaman belajar serta pendaftaran yang terus disempurnakan.</p></article>
                </div>
            </div>
        </section>

        <!-- Ekosistem dan ketersediaan fitur -->
        <section class="bg-slate-50 py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="mx-auto max-w-3xl text-center"><p class="text-xs font-extrabold uppercase tracking-[.18em] text-indigo-700">Ekosistem MENTORA</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Lebih dari Sekadar Katalog Beasiswa</h2><p class="mt-4 text-sm leading-7 text-slate-600">Sebuah platform yang dibangun bertahap untuk mendampingi pengguna sejak eksplorasi hingga persiapan pendaftaran.</p></div>
                <div class="mt-12 grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    <div class="space-y-2">
                        <div v-for="item in capabilities" :key="item.title" class="flex gap-4 border-b border-slate-200 py-5 last:border-b-0">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700"><component :is="item.icon" :size="20" /></span>
                            <div><div class="flex flex-wrap items-center gap-2"><h3 class="font-bold text-slate-900">{{ item.title }}</h3><span class="rounded-full px-2 py-0.5 text-[10px] font-bold" :class="item.status === 'Tersedia' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">{{ item.status }}</span></div><p class="mt-2 text-sm leading-6 text-slate-600">{{ item.description }}</p></div>
                        </div>
                    </div>
                    <div class="rounded-[26px] border border-indigo-100 bg-white p-4 shadow-[0_24px_60px_-28px_rgba(49,46,129,.25)] sm:p-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4"><div class="flex gap-1.5"><span class="size-2.5 rounded-full bg-rose-300"></span><span class="size-2.5 rounded-full bg-amber-300"></span><span class="size-2.5 rounded-full bg-emerald-300"></span></div><span class="text-[10px] font-semibold text-slate-400">Ilustrasi antarmuka MENTORA</span></div>
                        <div class="mt-5 grid grid-cols-[86px_1fr] gap-4 sm:grid-cols-[120px_1fr]">
                            <div class="rounded-xl bg-indigo-50 p-2"><div class="rounded-lg bg-indigo-700 p-2 text-center text-[10px] font-bold text-white">Ringkasan</div><div v-for="tab in ['Beasiswa', 'Event', 'Artikel']" :key="tab" class="mt-2 rounded-lg bg-white px-2 py-2 text-[10px] text-slate-600">{{ tab }}</div></div>
                            <div class="min-w-0"><p class="text-[11px] font-bold text-indigo-700">Halo, Pejuang Beasiswa!</p><h3 class="mt-2 text-base font-bold text-slate-900">Mulai eksplorasi peluangmu</h3><p class="mt-2 text-[11px] text-slate-500">Cari program dan siapkan langkah selanjutnya.</p><div class="mt-5 space-y-2"><div v-for="(task, i) in [{ icon: Search, name: 'Cari peluang studi' }, { icon: BookOpen, name: 'Baca panduan persiapan' }, { icon: CalendarCheck2, name: 'Temukan kegiatan' }]" :key="i" class="flex items-center gap-2 rounded-lg border border-slate-100 p-2.5"><span class="rounded-lg bg-indigo-50 p-1.5 text-indigo-700"><component :is="task.icon" :size="14" /></span><span class="text-[11px] font-semibold text-slate-700">{{ task.name }}</span><Check :size="14" class="ml-auto text-emerald-600" /></div></div></div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex flex-wrap justify-center gap-3"><Link href="/beasiswa" class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-indigo-700 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-800">Jelajahi Beasiswa <ArrowRight :size="16" /></Link><Link href="/fitur" class="inline-flex min-h-11 items-center rounded-xl border border-indigo-200 bg-white px-5 py-3 text-sm font-bold text-indigo-700 hover:bg-indigo-50">Lihat Semua Fitur</Link></div>
            </div>
        </section>

        <!-- Jenjang -->
        <section class="bg-white py-20 lg:py-24"><div class="mx-auto max-w-7xl px-5 sm:px-8"><div class="text-center"><p class="text-xs font-extrabold uppercase tracking-[.18em] text-indigo-700">Untuk Siapa?</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Dibangun untuk Berbagai Tahap Pendidikan</h2><p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-600">Kebutuhan setiap pelajar berbeda. MENTORA dirancang mendukung pencarian peluang dari jenjang awal hingga pendidikan lanjut.</p></div><div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4"><article v-for="level in educationLevels" :key="level.title" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700"><component :is="level.icon" :size="20" /></span><h3 class="mt-5 font-bold text-slate-900">{{ level.title }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ level.description }}</p></article></div>
                <div class="mt-12 grid gap-7 overflow-hidden rounded-[26px] bg-gradient-to-r from-indigo-800 to-indigo-500 p-8 text-white md:grid-cols-[1fr_0.9fr] md:items-center sm:p-11"><div><p class="text-xs font-bold uppercase tracking-widest text-indigo-100">Komitmen MENTORA</p><h3 class="mt-3 max-w-md text-2xl font-extrabold sm:text-3xl">Kepercayaan Dimulai dari Informasi yang Jelas.</h3><p class="mt-4 max-w-lg text-sm leading-7 text-indigo-100">MENTORA dibangun sebagai platform yang mengedepankan transparansi, rujukan sumber, dan pengelolaan informasi yang bertanggung jawab.</p></div><div class="space-y-3"><div v-for="text in ['Sumber informasi dapat ditelusuri', 'Status fitur disampaikan secara transparan', 'Keputusan penerimaan tetap pada penyelenggara']" :key="text" class="flex items-center gap-3 rounded-xl bg-white/15 p-3.5 text-xs font-semibold sm:text-sm"><CheckCircle2 :size="18" class="shrink-0" />{{ text }}</div></div></div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="bg-slate-50 py-20 lg:py-24"><div class="mx-auto max-w-4xl px-5 sm:px-8"><div class="text-center"><p class="text-xs font-extrabold uppercase tracking-[.18em] text-indigo-700">FAQ</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Pertanyaan tentang MENTORA</h2><p class="mt-4 text-sm leading-7 text-slate-600">Kenali cara kerja dan tujuan platform sebelum memulai perjalanan beasiswa.</p></div><div class="mt-10 space-y-3"><div v-for="(faq, index) in faqs" :key="index" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><h3><button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-5 text-left text-sm font-bold text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-[-3px] focus-visible:outline-indigo-700 sm:px-6" :aria-expanded="activeFaq === index" :aria-controls="`mentora-about-faq-${index}`" @click="toggleFaq(index)"><span>{{ faq.question }}</span><ChevronDown :size="18" class="shrink-0 text-indigo-700 transition-transform" :class="activeFaq === index ? 'rotate-180' : ''" aria-hidden="true" /></button></h3><div v-show="activeFaq === index" :id="`mentora-about-faq-${index}`" class="border-t border-slate-100 px-5 pb-5 pt-4 text-sm leading-7 text-slate-600 sm:px-6">{{ faq.answer }}</div></div></div></div></section>

        <!-- CTA akhir -->
        <section class="bg-white py-16"><div class="mx-auto max-w-7xl px-5 sm:px-8"><div class="flex flex-col items-start justify-between gap-7 overflow-hidden rounded-[26px] bg-gradient-to-r from-indigo-800 to-indigo-500 p-8 text-white sm:p-11 lg:flex-row lg:items-center"><div><h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Mulai Langkah Besarmu Bersama MENTORA.</h2><p class="mt-3 max-w-xl text-sm leading-7 text-indigo-100">Temukan peluang pendidikan, baca panduan, dan rencanakan persiapan berdasarkan informasi yang tersedia.</p></div><Link href="/beasiswa" class="inline-flex min-h-12 shrink-0 items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50">Jelajahi Beasiswa <ArrowRight :size="17" aria-hidden="true" /></Link></div></div></section>
    </PublicLayout>
</template>
