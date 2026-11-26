<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { dashboard, login, register } from '@/routes';
import MentoraBrand from './MentoraBrand.vue';

const page = usePage();
const menuOpen = ref(false);

// S0-06: only published routes are clickable. Enable the others as their pages are built.
const navigation = [
    { label: 'Beranda', href: '/', enabled: true },
    { label: 'Beasiswa', href: '/beasiswa', enabled: true },
    { label: 'Fitur', href: '/fitur', enabled: true },
    { label: 'Event', href: '/event', enabled: true },
    { label: 'Artikel', href: '/artikel', enabled: true },
    { label: 'Tentang', href: '/tentang', enabled: true },
] as const;

function isActive(href: string): boolean {
    return page.url.split('?')[0] === href;
}
</script>

<template>
    <a
        href="#main-content"
        class="sr-only rounded-lg bg-white px-4 py-2 text-mentora-primary focus:not-sr-only focus:absolute focus:left-4 focus:top-2 focus:z-[60]"
    >
        Lewati navigasi
    </a>

    <header class="sticky top-0 z-50 border-b border-mentora-border bg-white/95 backdrop-blur-md">
        <div class="mx-auto flex min-h-18 w-full max-w-7xl items-center justify-between gap-4 px-5 md:px-8">
            <Link href="/" aria-label="MENTORA - Beranda" @click="menuOpen = false">
                <MentoraBrand />
            </Link>

            <nav aria-label="Navigasi utama" class="hidden items-center gap-1 lg:flex">
                <template v-for="item in navigation" :key="item.label">
                    <Link
                        v-if="item.enabled"
                        :href="item.href"
                        :aria-current="isActive(item.href) ? 'page' : undefined"
                        class="rounded-xl px-3.5 py-2 text-sm font-semibold transition-colors"
                        :class="isActive(item.href)
                            ? 'bg-mentora-lavender text-mentora-primary'
                            : 'text-mentora-navy hover:bg-mentora-lavender'"
                    >
                        {{ item.label }}
                    </Link>
                    <span
                        v-else
                        :title="`${item.label} sedang disiapkan`"
                        aria-disabled="true"
                        class="cursor-not-allowed px-3.5 py-2 text-sm font-medium text-mentora-muted"
                    >
                        {{ item.label }}
                    </span>
                </template>
            </nav>

            <div class="hidden items-center gap-2.5 lg:flex">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-xl bg-mentora-primary px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-mentora-primary-hover"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="rounded-xl border border-mentora-primary px-5 py-2.5 text-sm font-semibold text-mentora-primary transition-colors hover:bg-mentora-lavender"
                    >
                        Masuk
                    </Link>
                    <Link
                        :href="register()"
                        class="rounded-xl bg-mentora-primary px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-mentora-primary-hover"
                    >
                        Daftar Gratis
                    </Link>
                </template>
            </div>

            <button
                type="button"
                aria-controls="mentora-mobile-nav"
                :aria-expanded="menuOpen"
                :aria-label="menuOpen ? 'Tutup menu' : 'Buka menu'"
                class="inline-flex size-11 items-center justify-center rounded-xl border border-mentora-border text-mentora-navy focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary lg:hidden"
                @click="menuOpen = !menuOpen"
            >
                <X v-if="menuOpen" :size="21" aria-hidden="true" />
                <Menu v-else :size="21" aria-hidden="true" />
            </button>
        </div>

        <nav
            v-if="menuOpen"
            id="mentora-mobile-nav"
            aria-label="Navigasi seluler"
            class="border-t border-mentora-border bg-white px-5 pb-5 pt-3 lg:hidden"
        >
            <div class="mx-auto flex max-w-7xl flex-col gap-1">
                <template v-for="item in navigation" :key="item.label">
                    <Link
                        v-if="item.enabled"
                        :href="item.href"
                        class="rounded-lg px-3 py-2.5 text-sm font-semibold text-mentora-navy hover:bg-mentora-lavender"
                        @click="menuOpen = false"
                    >
                        {{ item.label }}
                    </Link>
                    <span
                        v-else
                        aria-disabled="true"
                        class="px-3 py-2.5 text-sm text-mentora-muted"
                    >
                        {{ item.label }} · Segera tersedia
                    </span>
                </template>
                <div class="mt-3 flex flex-wrap gap-2 border-t border-mentora-border pt-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-xl bg-mentora-primary px-5 py-2.5 text-sm font-semibold text-white"
                        @click="menuOpen = false"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-xl border border-mentora-primary px-5 py-2.5 text-sm font-semibold text-mentora-primary"
                            @click="menuOpen = false"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-xl bg-mentora-primary px-5 py-2.5 text-sm font-semibold text-white"
                            @click="menuOpen = false"
                        >
                            Daftar Gratis
                        </Link>
                    </template>
                </div>
            </div>
        </nav>
    </header>
</template>
