<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowLeft, CheckCircle2, CircleAlert,
    LayoutDashboard, LogOut, Mail, Menu, RefreshCw,
    ShieldCheck, UserPlus, UsersRound, X,
} from 'lucide-vue-next';
import MentoraBrand from '@/components/mentora/MentoraBrand.vue';

type RoleCode = 'editor' | 'verifier' | 'admin';
type UserRow = {
    id: number;
    name: string;
    email: string;
    roles: string[];
    email_verified: boolean;
    is_staff_invited: boolean;
    created_at: string | null;
};
type PageLink = { url: string | null; label: string; active: boolean };
type Paginator = {
    data: UserRow[];
    current_page: number;
    last_page: number;
    total: number;
    links: PageLink[];
};

const props = defineProps<{
    administrator: { name: string };
    users: Paginator;
    notice?: string | null;
    warning?: string | null;
    mailerIsLocal: boolean;
}>();

const roleLabels: Record<RoleCode, string> = {
    editor: 'Editor',
    verifier: 'Verifier',
    admin: 'Admin',
};

const form = useForm<{ name: string; email: string; role_code: RoleCode }>({
    name: '',
    email: '',
    role_code: 'editor',
});
const mobileMenuOpen = ref(false);
const searching = ref('');
const resendingId = ref<number | null>(null);
const rows = computed(() => props.users.data.filter((user) =>
    `${user.name} ${user.email} ${user.roles.join(' ')}`.toLowerCase().includes(searching.value.toLowerCase().trim())
));
const initials = computed(() => props.administrator.name.trim().split(/\s+/).slice(0, 2)
    .map((name) => name.slice(0, 1)).join('').toUpperCase() || 'SA');

function invite() {
    if (form.processing) return;
    form.post('/superadmin/staff-invitations', {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'email'),
    });
}

function resend(user: UserRow) {
    if (resendingId.value !== null || user.email_verified || !user.is_staff_invited) return;
    resendingId.value = user.id;
    router.post(`/superadmin/staff-invitations/${user.id}/resend`, {}, {
        preserveScroll: true,
        onFinish: () => { resendingId.value = null; },
    });
}

function pageLabel(label: string): string {
    return label.replace(/&laquo;/g, '«').replace(/&raquo;/g, '»').replace(/&nbsp;/g, ' ');
}
</script>

<template>
    <Head title="Manajemen Akun - SuperAdmin MENTORA" />

    <div class="min-h-screen bg-[#f7f9fe] text-[#111b38] antialiased">
        <button
            v-if="mobileMenuOpen"
            type="button"
            aria-label="Tutup menu"
            class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
            @click="mobileMenuOpen = false"
        />

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-[#e6ebf5] bg-white transition-transform duration-200 lg:translate-x-0"
            :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            aria-label="Navigasi SuperAdmin"
        >
            <div class="flex h-20 shrink-0 items-center justify-between border-b border-slate-100 px-6">
                <Link href="/" aria-label="Beranda MENTORA"><MentoraBrand /></Link>
                <button type="button" class="rounded-lg p-2 lg:hidden" aria-label="Tutup menu" @click="mobileMenuOpen = false"><X :size="20" /></button>
            </div>
            <nav class="flex-1 px-3 py-5" aria-label="Menu utama SuperAdmin">
                <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">Administrasi</p>
                <Link href="/superadmin/users" aria-current="page" class="flex min-h-11 items-center gap-3 rounded-xl bg-[#2b6dff] px-3 text-[13px] font-bold text-white">
                    <UsersRound :size="18" aria-hidden="true" /> Manajemen Pengguna
                </Link>
                <Link href="/dashboard" class="mt-1 flex min-h-11 items-center gap-3 rounded-xl px-3 text-[13px] font-medium text-slate-700 hover:bg-blue-50">
                    <LayoutDashboard :size="18" aria-hidden="true" /> Dashboard Umum
                </Link>
                <div class="mt-6 border-t border-slate-100 pt-5">
                    <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">Akun</p>
                    <Link href="/settings/profile" class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-[13px] font-medium text-slate-700 hover:bg-blue-50">
                        <ShieldCheck :size="18" aria-hidden="true" /> Pengaturan Akun
                    </Link>
                    <Link href="/logout" method="post" as="button" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 text-left text-[13px] font-semibold text-red-600 hover:bg-red-50">
                        <LogOut :size="18" aria-hidden="true" /> Keluar
                    </Link>
                </div>
            </nav>
            <div class="m-3 rounded-2xl border border-indigo-100 bg-indigo-50/80 p-4">
                <p class="text-xs font-bold text-indigo-900">Akses Terbatas</p>
                <p class="mt-1 text-xs leading-5 text-slate-600">Hanya SuperAdmin terverifikasi yang dapat mengundang akun staf dan melihat daftar pengguna.</p>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-64">
            <header class="sticky top-0 z-30 flex min-h-20 items-center justify-between gap-3 border-b border-[#e7ecf5] bg-white/95 px-4 backdrop-blur sm:px-6 lg:px-7">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" class="rounded-xl border border-slate-200 p-2.5 lg:hidden" aria-label="Buka navigasi" @click="mobileMenuOpen = true"><Menu :size="20" /></button>
                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-extrabold tracking-tight sm:text-2xl">Manajemen Pengguna</h1>
                        <p class="hidden text-xs text-slate-500 sm:block">Kelola undangan akun staf internal MENTORA secara aman.</p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <span class="hidden items-center gap-1 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 sm:inline-flex"><ShieldCheck :size="14" /> SuperAdmin</span>
                    <span class="flex size-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-extrabold text-indigo-700" :aria-label="administrator.name">{{ initials }}</span>
                </div>
            </header>

            <main class="mx-auto max-w-[1500px] space-y-5 px-4 py-6 sm:px-6 lg:px-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-[#3a40c4]">Identity &amp; RBAC</p>
                        <h2 class="mt-1 text-xl font-extrabold tracking-tight sm:text-2xl">Undangan Akun Internal</h2>
                        <p class="mt-1 max-w-2xl text-xs leading-6 text-slate-500">Undang Editor, Verifier, atau Admin. Akun Member dan Guardian tetap mendaftar sendiri melalui halaman publik.</p>
                    </div>
                    <Link href="/dashboard" class="inline-flex items-center gap-2 rounded-xl border border-indigo-100 bg-white px-4 py-2.5 text-xs font-bold text-indigo-700 hover:bg-indigo-50"><ArrowLeft :size="15" /> Dashboard</Link>
                </div>

                <section v-if="notice" aria-live="polite" class="flex items-start gap-2 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    <CheckCircle2 :size="18" class="mt-0.5 shrink-0" /> {{ notice }}
                </section>
                <section v-if="warning" role="alert" class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <CircleAlert :size="18" class="mt-0.5 shrink-0" /> {{ warning }}
                </section>
                <section v-if="mailerIsLocal" class="flex items-start gap-2 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                    <Mail :size="18" class="mt-0.5 shrink-0" />
                    <span><strong>Mode lokal:</strong> tautan aktivasi tersimpan di <code>storage/logs/laravel.log</code> atau mailer pengujian, bukan dikirim ke Gmail. Jangan membagikan tautan tersebut.</span>
                </section>

                <div class="grid items-start gap-5 xl:grid-cols-[380px_minmax(0,1fr)]">
                    <section class="rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm" aria-labelledby="invite-title">
                        <div class="flex items-center gap-3">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><UserPlus :size="22" /></span>
                            <div><h3 id="invite-title" class="text-base font-extrabold">Buat Undangan Staf</h3><p class="mt-1 text-xs text-slate-500">Khusus akun baru yang belum terdaftar</p></div>
                        </div>
                        <form class="mt-5 space-y-4" @submit.prevent="invite">
                            <div>
                                <label for="staff-name" class="block text-xs font-bold text-slate-800">Nama Lengkap</label>
                                <input id="staff-name" v-model="form.name" type="text" required minlength="3" maxlength="255" autocomplete="name" placeholder="Nama staf MENTORA" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label for="staff-email" class="block text-xs font-bold text-slate-800">Alamat Email</label>
                                <input id="staff-email" v-model="form.email" type="email" required maxlength="254" autocomplete="off" placeholder="email@domain.com" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label for="staff-role" class="block text-xs font-bold text-slate-800">Role Akun</label>
                                <select id="staff-role" v-model="form.role_code" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    <option v-for="(label, code) in roleLabels" :key="code" :value="code">{{ label }}</option>
                                </select>
                                <p v-if="form.errors.role_code" class="mt-1 text-xs text-red-600">{{ form.errors.role_code }}</p>
                            </div>
                            <p class="rounded-xl bg-slate-50 px-3.5 py-3 text-xs leading-5 text-slate-600">Staf akan menentukan kata sandinya sendiri melalui tautan aktivasi. Sistem tidak membuat atau menampilkan password sementara.</p>
                            <button type="submit" :disabled="form.processing" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#3439c1] px-4 text-sm font-bold text-white transition hover:bg-indigo-800 disabled:cursor-not-allowed disabled:opacity-50">
                                <UserPlus :size="17" /> {{ form.processing ? 'Memproses...' : 'Buat & Kirim Undangan' }}
                            </button>
                        </form>
                    </section>

                    <section class="min-w-0 rounded-2xl border border-[#e4e9f3] bg-white p-5 shadow-sm" aria-labelledby="accounts-title">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 id="accounts-title" class="text-base font-extrabold">Daftar Pengguna MENTORA</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ users.total }} akun terdaftar · Halaman {{ users.current_page }} dari {{ users.last_page }}</p>
                            </div>
                            <input v-model="searching" type="search" aria-label="Cari pada halaman ini" placeholder="Cari halaman ini..." class="min-h-10 w-full rounded-xl border border-slate-200 px-3 text-xs outline-none focus:border-indigo-500 sm:w-48" />
                        </div>
                        <p class="mt-3 text-[11px] text-slate-500">Pencarian hanya memfilter data pada halaman yang sedang ditampilkan.</p>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th scope="col" class="rounded-l-lg px-3 py-3">Pengguna</th>
                                        <th scope="col" class="px-3 py-3">Role</th>
                                        <th scope="col" class="px-3 py-3">Status</th>
                                        <th scope="col" class="rounded-r-lg px-3 py-3 text-right">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="user in rows" :key="user.id" class="align-top">
                                        <td class="px-3 py-4"><p class="font-bold text-slate-900">{{ user.name }}</p><p class="mt-1 break-all text-slate-500">{{ user.email }}</p><p class="mt-1 text-[10px] text-slate-400">{{ user.created_at }}</p></td>
                                        <td class="px-3 py-4"><span v-for="role in user.roles" :key="role" class="mb-1 mr-1 inline-block rounded-full border border-indigo-100 bg-indigo-50 px-2 py-1 text-[10px] font-bold text-indigo-700">{{ role }}</span><span v-if="!user.roles.length" class="text-slate-400">Tanpa role</span></td>
                                        <td class="px-3 py-4"><span class="inline-flex rounded-full px-2 py-1 text-[10px] font-bold" :class="user.email_verified ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700'">{{ user.email_verified ? 'Terverifikasi' : 'Belum verifikasi' }}</span></td>
                                        <td class="px-3 py-4 text-right">
                                            <button v-if="user.is_staff_invited && !user.email_verified" type="button" :disabled="resendingId !== null" class="inline-flex items-center gap-1 whitespace-nowrap rounded-lg border border-indigo-200 px-2.5 py-2 font-semibold text-indigo-700 hover:bg-indigo-50 disabled:opacity-50" @click="resend(user)"><RefreshCw :size="13" /> {{ resendingId === user.id ? 'Mengirim...' : 'Kirim Ulang' }}</button>
                                            <span v-else class="text-[11px] text-slate-400">—</span>
                                        </td>
                                    </tr>
                                    <tr v-if="rows.length === 0"><td colspan="4" class="px-3 py-10 text-center text-slate-500">Tidak ada pengguna pada halaman ini.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="users.last_page > 1" class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                            <span class="text-[11px] text-slate-500">Total {{ users.total }} akun</span>
                            <nav class="flex flex-wrap items-center gap-1" aria-label="Paginasi pengguna">
                                <template v-for="(link, i) in users.links" :key="i">
                                    <Link v-if="link.url" :href="link.url" :aria-current="link.active ? 'page' : undefined" class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg border px-2 text-xs font-bold" :class="link.active ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">{{ pageLabel(link.label) }}</Link>
                                    <span v-else class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg border border-slate-100 px-2 text-xs text-slate-300">{{ pageLabel(link.label) }}</span>
                                </template>
                            </nav>
                        </div>
                    </section>
                </div>
                <footer class="flex items-center gap-1 text-[11px] text-slate-500"><CheckCircle2 :size="14" class="text-indigo-600" /> Audit undangan tersimpan tanpa token reset atau password.</footer>
            </main>
        </div>
    </div>
</template>
