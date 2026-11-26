<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowRight,
    BookOpen,
    Bookmark,
    CalendarDays,
    CheckCircle2,
    ChevronRight,
    CircleHelp,
    ClipboardCheck,
    Clock3,
    Compass,
    FileText,
    GraduationCap,
    LayoutDashboard,
    LogOut,
    Menu,
    Search,
    Settings,
    ShieldCheck,
    Sparkles,
    Target,
    UserRound,
    X,
    Zap,
} from 'lucide-vue-next';
import MentoraBrand from '@/components/mentora/MentoraBrand.vue';

type MemberInfo = { name: string };
const props = defineProps<{ member: MemberInfo }>();
const mobileMenuOpen = ref(false);
const firstName = computed(() => props.member.name.trim().split(/\s+/)[0] || 'Member');
const initials = computed(() => props.member.name.trim().split(/\s+/).slice(0, 2).map(part => part.slice(0, 1)).join('').toUpperCase() || 'M');

// Never link to an unimplemented route. An item without `href` is disabled.
const navigation = [
    { label: 'Dashboard', icon: LayoutDashboard, href: '/member/dashboard', active: true },
    { label: 'Beasiswa', icon: GraduationCap, href: '/beasiswa', active: false },
    { label: 'Rekomendasi', icon: Sparkles, href: null, active: false },
    { label: 'Eligibility', icon: ShieldCheck, href: null, active: false },
    { label: 'Tracker Pendaftaran', icon: ClipboardCheck, href: null, active: false },
    { label: 'Tryout', icon: Target, href: null, active: false },
    { label: 'Event & Webinar', icon: CalendarDays, href: '/event', active: false },
    { label: 'AI Essay Reviewer', icon: FileText, href: null, active: false },
    { label: 'Artikel & Panduan', icon: BookOpen, href: '/artikel', active: false },
] as const;

const summaryCards = [
    { label: 'Beasiswa Tersimpan', icon: Bookmark, note: 'Fitur simpan akan hadir', tint: 'bg-blue-50 text-blue-600' },
    { label: 'Pendaftaran Aktif', icon: ClipboardCheck, note: 'Tracker belum tersedia', tint: 'bg-violet-50 text-violet-600' },
    { label: 'Deadline Terdekat', icon: Clock3, note: 'Belum ada tenggat tercatat', tint: 'bg-orange-50 text-orange-600' },
    { label: 'Rekomendasi Baru', icon: Sparkles, note: 'Matching sedang disiapkan', tint: 'bg-green-50 text-green-600' },
] as const;

const quickActions = [
    { label: 'Cari Beasiswa', href: '/beasiswa', icon: Search },
    { label: 'Jelajahi Event', href: '/event', icon: CalendarDays },
    { label: 'Baca Artikel', href: '/artikel', icon: BookOpen },
    { label: 'Profil Saya', href: '/settings/profile', icon: UserRound },
] as const;
</script>

<template>
    <Head title="Dashboard Member - MENTORA" />

    <div class="min-h-screen bg-[#f7f9fe] text-[#111b38] antialiased">
        <!-- Mobile backdrop -->
        <button
            v-if="mobileMenuOpen"
            type="button"
            class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
            aria-label="Tutup navigasi dashboard"
            @click="mobileMenuOpen = false"
        />

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-[#e6ebf5] bg-white shadow-lg transition-transform duration-200 lg:translate-x-0 lg:shadow-none"
            :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            aria-label="Navigasi Member"
        >
            <div class="flex h-20 shrink-0 items-center justify-between border-b border-slate-100 px-6">
                <Link href="/" aria-label="Ke Beranda MENTORA" @click="mobileMenuOpen = false">
                    <MentoraBrand />
                </Link>
                <button type="button" class="rounded-lg p-2 text-slate-600 lg:hidden" aria-label="Tutup menu" @click="mobileMenuOpen = false">
                    <X :size="20" aria-hidden="true" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-5" aria-label="Menu utama Member">
                <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">Menu utama</p>
                <ul class="space-y-1">
                    <li v-for="item in navigation" :key="item.label">
                        <Link
                            v-if="item.href"
                            :href="item.href"
                            :aria-current="item.active ? 'page' : undefined"
                            class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-[13px] font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            :class="item.active ? 'bg-[#2b6dff] text-white shadow-sm' : 'text-slate-700 hover:bg-blue-50 hover:text-[#264bd3]'"
                            @click="mobileMenuOpen = false"
                        >
                            <component :is="item.icon" :size="18" aria-hidden="true" />
                            <span class="min-w-0 flex-1">{{ item.label }}</span>
                        </Link>
                        <span
                            v-else
                            class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-[13px] font-medium text-slate-400"
                            aria-disabled="true"
                        >
                            <component :is="item.icon" :size="18" aria-hidden="true" />
                            <span class="min-w-0 flex-1">{{ item.label }}</span>
                            <span class="rounded-full bg-slate-100 px-1.5 py-0.5 text-[9px] font-semibold text-slate-500">Segera</span>
                        </span>
                    </li>
                </ul>

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">Akun</p>
                    <Link href="/settings/profile" class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-[13px] font-medium text-slate-700 transition hover:bg-blue-50">
                        <UserRound :size="18" aria-hidden="true" /> Profil Saya
                    </Link>
                    <Link href="/settings/profile" class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-[13px] font-medium text-slate-700 transition hover:bg-blue-50">
                        <Settings :size="18" aria-hidden="true" /> Pengaturan
                    </Link>
                    <Link href="/tentang" class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-[13px] font-medium text-slate-700 transition hover:bg-blue-50">
                        <CircleHelp :size="18" aria-hidden="true" /> Bantuan
                    </Link>
                    <Link href="/logout" method="post" as="button" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 text-left text-[13px] font-semibold text-red-600 transition hover:bg-red-50">
                        <LogOut :size="18" aria-hidden="true" /> Keluar
                    </Link>
                </div>
            </nav>
            <div class="m-3 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-white p-4">
                <p class="text-xs font-bold text-indigo-900">Perjalananmu dimulai di sini</p>
                <p class="mt-1 text-xs leading-5 text-slate-600">Temukan peluang dan siapkan dokumen beasiswamu.</p>
                <Link href="/beasiswa" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-indigo-700 hover:underline">
                    Jelajahi beasiswa <ArrowRight :size="13" aria-hidden="true" />
                </Link>
            </div>
        </aside>

        <!-- Main -->
        <div class="min-h-screen lg:pl-64">
            <header class="sticky top-0 z-30 flex min-h-20 items-center justify-between gap-3 border-b border-[#e7ecf5] bg-white/95 px-4 backdrop-blur sm:px-6 lg:px-7">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" class="rounded-xl border border-slate-200 p-2.5 text-slate-800 lg:hidden" :aria-expanded="mobileMenuOpen" aria-label="Buka navigasi Member" @click="mobileMenuOpen = true">
                        <Menu :size="20" aria-hidden="true" />
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-extrabold tracking-tight sm:text-2xl">Selamat datang, {{ firstName }}! <span aria-hidden="true">👋</span></h1>
                        <p class="mt-0.5 hidden text-xs text-slate-500 sm:block">Jelajahi peluang beasiswa dan lanjutkan persiapan studimu bersama MENTORA.</p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <span class="hidden items-center gap-1 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 sm:inline-flex">
                        <CheckCircle2 :size="14" aria-hidden="true" /> Member
                    </span>
                    <div class="flex size-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-extrabold text-indigo-700" :aria-label="`Akun ${member.name}`">{{ initials }}</div>
                </div>
            </header>

            <main id="main-content" class="mx-auto max-w-[1540px] space-y-5 px-4 py-6 sm:px-6 lg:px-7">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-[#3a40c4]">Dashboard Member</p>
                        <h2 class="mt-1 text-xl font-extrabold tracking-tight sm:text-2xl">Ringkasan Perjalanan Beasiswamu</h2>
                    </div>
                    <Link href="/beasiswa" class="hidden items-center gap-2 rounded-xl bg-[#3439c1] px-4 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-800 sm:inline-flex">
                        Cari Beasiswa <ArrowRight :size="15" aria-hidden="true" />
                    </Link>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article v-for="card in summaryCards" :key="card.label" class="rounded-2xl border border-[#e4e9f3] bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex size-11 items-center justify-center rounded-xl" :class="card.tint">
                                <component :is="card.icon" :size="21" aria-hidden="true" />
                            </div>
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-500">Segera</span>
                        </div>
                        <h3 class="mt-4 text-sm font-bold">{{ card.label }}</h3>
                        <p class="mt-1 text-2xl font-extrabold text-slate-400" aria-label="Belum tersedia">—</p>
                        <p class="mt-1 text-xs text-slate-500">{{ card.note }}</p>
                    </article>
                </div>

                <div class="grid items-start gap-4 xl:grid-cols-3">
                    <div class="space-y-4 xl:col-span-2">
                        <section class="rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm sm:p-6" aria-labelledby="progress-heading">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 id="progress-heading" class="text-base font-extrabold sm:text-lg">Progress Persiapan Beasiswa</h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Statistik progres akan tampil setelah fitur Tracker tersedia.</p>
                                </div>
                                <span class="rounded-full bg-violet-50 px-3 py-1.5 text-[11px] font-semibold text-violet-700">Dalam pengembangan</span>
                            </div>
                            <div class="mt-5 flex min-h-44 flex-col items-center justify-center rounded-2xl border border-dashed border-indigo-200 bg-gradient-to-r from-indigo-50/50 to-white px-5 py-8 text-center">
                                <div class="flex size-14 items-center justify-center rounded-2xl bg-white text-[#3439c1] shadow-sm"><ClipboardCheck :size="27" aria-hidden="true" /></div>
                                <p class="mt-3 text-sm font-bold">Belum ada progres persiapan yang tercatat</p>
                                <p class="mt-2 max-w-lg text-xs leading-6 text-slate-500">Nantinya, perkembangan dokumen, esai, dan pendaftaranmu akan ditampilkan di sini. Tidak ada data contoh yang dihitung sebagai progres nyata.</p>
                                <Link href="/beasiswa" class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-[#3439c1] hover:underline">Mulai dari katalog beasiswa <ArrowRight :size="14" aria-hidden="true" /></Link>
                            </div>
                        </section>

                        <div class="grid gap-4 md:grid-cols-2">
                            <section class="rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm" aria-labelledby="deadline-heading">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 id="deadline-heading" class="flex items-center gap-2 text-sm font-extrabold"><CalendarDays :size="19" class="text-[#315add]" aria-hidden="true" /> Deadline Penting</h3>
                                    <Link href="/beasiswa" class="text-xs font-bold text-[#3439c1] hover:underline">Lihat beasiswa</Link>
                                </div>
                                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-5">
                                    <p class="text-sm font-semibold">Belum ada tenggat yang dipantau</p>
                                    <p class="mt-2 text-xs leading-5 text-slate-500">Deadline personal akan tersedia setelah fitur Tracker diluncurkan.</p>
                                </div>
                            </section>
                            <section class="rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm" aria-labelledby="matching-heading">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 id="matching-heading" class="flex items-center gap-2 text-sm font-extrabold"><Sparkles :size="19" class="text-[#315add]" aria-hidden="true" /> Rekomendasi untukmu</h3>
                                    <span class="text-[11px] font-semibold text-slate-400">Segera</span>
                                </div>
                                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-5">
                                    <p class="text-sm font-semibold">Rekomendasi personal belum tersedia</p>
                                    <p class="mt-2 text-xs leading-5 text-slate-500">Sambil menunggu Matching, kamu tetap dapat menelusuri semua beasiswa publik.</p>
                                    <Link href="/beasiswa" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-[#3439c1] hover:underline">Jelajahi beasiswa <ArrowRight :size="13" aria-hidden="true" /></Link>
                                </div>
                            </section>
                        </div>
                        <section class="rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm" aria-labelledby="steps-heading">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 id="steps-heading" class="flex items-center gap-2 text-base font-extrabold"><Target :size="20" class="text-[#315add]" aria-hidden="true" /> Langkah Persiapan</h3>
                                    <p class="mt-1 text-xs text-slate-500">Panduan umum, bukan daftar tugas yang disimpan ke akunmu.</p>
                                </div>
                            </div>
                            <div class="mt-4 divide-y divide-slate-100">
                                <div class="flex items-start gap-3 py-3"><span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700"><UserRound :size="17" aria-hidden="true" /></span><div><p class="text-sm font-bold">Periksa informasi akunmu</p><p class="mt-1 text-xs text-slate-500">Pastikan nama dan email yang digunakan sudah benar.</p></div></div>
                                <div class="flex items-start gap-3 py-3"><span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700"><Search :size="17" aria-hidden="true" /></span><div><p class="text-sm font-bold">Telusuri peluang beasiswa</p><p class="mt-1 text-xs text-slate-500">Gunakan filter jenjang dan persyaratan pada Katalog Beasiswa.</p></div></div>
                                <div class="flex items-start gap-3 py-3"><span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-700"><FileText :size="17" aria-hidden="true" /></span><div><p class="text-sm font-bold">Siapkan dokumen pendukung</p><p class="mt-1 text-xs text-slate-500">Periksa dokumen yang diminta oleh penyelenggara masing-masing.</p></div></div>
                            </div>
                        </section>
                    </div>

                    <div class="space-y-4">
                        <section class="rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm" aria-labelledby="profile-heading">
                            <div class="flex items-start gap-3">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><UserRound :size="23" aria-hidden="true" /></span>
                                <div><h3 id="profile-heading" class="text-base font-extrabold">Informasi Profil</h3><p class="mt-1 text-xs leading-5 text-slate-500">Kelola data akun untuk mendukung persiapan beasiswamu.</p></div>
                            </div>
                            <div class="mt-4 rounded-xl bg-[#f5f7ff] px-4 py-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Kelengkapan profil</p>
                                <p class="mt-1 text-sm font-bold text-[#3439c1]">Belum dapat dihitung</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">Profil pendidikan akan dikembangkan pada tahap berikutnya.</p>
                            </div>
                            <Link href="/settings/profile" class="mt-4 flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#3439c1] px-3 text-sm font-bold text-[#3439c1] transition hover:bg-indigo-50">Pengaturan Profil <ArrowRight :size="16" aria-hidden="true" /></Link>
                        </section>

                        <section class="rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm" aria-labelledby="actions-heading">
                            <h3 id="actions-heading" class="flex items-center gap-2 text-base font-extrabold"><Zap :size="20" class="text-[#315add]" aria-hidden="true" /> Aksi Cepat</h3>
                            <div class="mt-4 grid grid-cols-2 gap-2.5">
                                <Link v-for="action in quickActions" :key="action.href" :href="action.href" class="flex min-h-24 flex-col items-center justify-center gap-2 rounded-xl border border-[#e6ebf5] bg-[#f9faff] p-2 text-center text-xs font-bold transition hover:border-indigo-200 hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-indigo-500">
                                    <component :is="action.icon" :size="23" class="text-[#2b58d9]" aria-hidden="true" />{{ action.label }}
                                </Link>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm" aria-labelledby="event-heading">
                            <div class="flex items-center justify-between gap-2">
                                <h3 id="event-heading" class="flex items-center gap-2 text-sm font-extrabold"><CalendarDays :size="20" class="text-[#315add]" aria-hidden="true" /> Event Mendatang</h3>
                                <Link href="/event" class="text-xs font-bold text-[#3439c1] hover:underline">Lihat semua</Link>
                            </div>
                            <div class="mt-4 rounded-xl bg-slate-50 px-4 py-5">
                                <p class="text-sm font-semibold">Jelajahi webinar dan workshop</p>
                                <p class="mt-2 text-xs leading-5 text-slate-500">Temukan jadwal yang benar-benar dipublikasikan pada Katalog Event.</p>
                                <Link href="/event" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-[#3439c1] hover:underline">Buka katalog <ChevronRight :size="14" aria-hidden="true" /></Link>
                            </div>
                        </section>
                        <section class="rounded-2xl bg-gradient-to-br from-[#3039b9] to-[#6374eb] p-5 text-white shadow-sm" aria-labelledby="guide-heading">
                            <div class="flex items-center gap-2"><Compass :size="20" aria-hidden="true" /><h3 id="guide-heading" class="text-sm font-extrabold">Butuh Panduan?</h3></div>
                            <p class="mt-2 text-xs leading-6 text-indigo-100">Artikel dan panduan MENTORA dapat membantu mempersiapkan pendaftaran beasiswamu.</p>
                            <Link href="/artikel" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-bold text-[#3439c1] transition hover:bg-indigo-50">Baca artikel <ArrowRight :size="14" aria-hidden="true" /></Link>
                        </section>
                    </div>
                </div>
                <p class="pb-3 text-center text-[11px] text-slate-400">© 2026 MENTORA. Dashboard Member — Fondasi UI. Statistik personal akan tersedia setelah modul terkait siap.</p>
            </main>
        </div>
    </div>
</template>
