<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import MentoraBrand from '@/components/mentora/MentoraBrand.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

// Desain MENTORA digunakan bersama untuk Login dan Register.
// Reset password, passkey, verifikasi email, dan 2FA tetap memakai tampilan asal.
const page = usePage();
const isRegisterPage = computed(() => page.component.toLowerCase() === 'auth/register');
const isMentoraAuthPage = computed(() => {
    const component = page.component.toLowerCase();
    return component === 'auth/login' || component === 'auth/register';
});
</script>

<template>
    <div
        v-if="isMentoraAuthPage"
        class="relative isolate flex min-h-svh items-center justify-center overflow-hidden bg-[#f8faff] px-4 py-9 font-sans text-[#111936] antialiased sm:px-6 sm:py-12"
    >
        <div
            aria-hidden="true"
            class="pointer-events-none absolute -top-44 -left-40 h-[450px] w-[450px] rounded-full bg-[#e7ecff]/70 blur-3xl"
        />
        <div
            aria-hidden="true"
            class="pointer-events-none absolute -right-48 -bottom-64 h-[550px] w-[550px] rounded-full bg-[#e9e7ff]/70 blur-3xl"
        />

        <div
            class="relative z-10 w-full"
            :class="isRegisterPage ? 'max-w-[600px]' : 'max-w-[550px]'"
        >
            <div class="mb-7 flex justify-center sm:mb-8">
                <Link
                    :href="home()"
                    aria-label="MENTORA — kembali ke Beranda"
                    class="rounded-lg outline-offset-4 focus-visible:outline-2 focus-visible:outline-[#3443d0]"
                >
                    <MentoraBrand />
                </Link>
            </div>

            <main
                class="rounded-[26px] border border-[#e6eaf5] bg-white px-6 py-7 shadow-[0_18px_65px_rgba(33,45,100,0.08)] sm:px-10 sm:py-9"
            >
                <header v-if="title || description" class="mb-7 text-center">
                    <h1
                        v-if="title"
                        class="text-[28px] leading-tight font-extrabold tracking-[-0.035em] text-[#111936] sm:text-[32px]"
                    >
                        {{ title }}
                    </h1>
                    <p
                        v-if="description"
                        class="mx-auto mt-3 max-w-[420px] text-sm leading-6 text-[#64708c] sm:text-[15px]"
                    >
                        {{ description }}
                    </p>
                </header>
                <slot />
            </main>

            <div
                class="mt-6 flex flex-wrap items-center justify-center gap-x-2 gap-y-1 text-center text-xs text-[#7e89a2]"
            >
                <span>© {{ new Date().getFullYear() }} MENTORA.</span>
                <span>Hak cipta dilindungi.</span>
                <span aria-hidden="true" class="hidden sm:inline">·</span>
                <Link :href="home()" class="font-semibold text-[#3443d0] hover:underline">
                    Kembali ke Beranda
                </Link>
            </div>
        </div>
    </div>

    <!-- Pertahankan layout bawaan untuk auth selain Login dan Register. -->
    <div
        v-else
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10"
    >
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <div
                            class="mb-1 flex h-9 w-9 items-center justify-center rounded-md"
                        >
                            <AppLogoIcon
                                class="size-9 fill-current text-[var(--foreground)] dark:text-white"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
