<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ArrowRight, LockKeyhole, Mail, ShieldCheck } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

// Properti ini dibaca oleh AuthLayout yang sudah terpasang di starter kit.
defineOptions({
    layout: {
        title: 'Selamat datang kembali!',
        description:
            'Masuk ke akun MENTORA untuk melanjutkan perjalanan mencari beasiswa dan kesempatan belajar.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Masuk ke MENTORA" />

    <div
        v-if="status"
        role="status"
        class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-5 font-medium text-emerald-800"
    >
        {{ status }}
    </div>

    <!-- Gunakan komponen passkey bawaan: alur autentikasinya tidak diubah. -->
    <div class="mentora-login-passkey">
        <PasskeyVerify
            label="Masuk menggunakan passkey"
            loading-label="Memverifikasi passkey..."
            separator="Atau lanjutkan dengan email"
        />
    </div>

    <!-- POST /login tetap melalui Fortify/Wayfinder bawaan Laravel. -->
    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-2">
            <Label for="email" class="text-sm font-semibold text-[#192443]">
                Alamat Email
            </Label>
            <div class="relative">
                <Mail
                    aria-hidden="true"
                    class="pointer-events-none absolute top-1/2 left-4 size-[18px] -translate-y-1/2 text-[#7885a5]"
                />
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="Masukkan alamat email"
                    class="h-12 rounded-xl border-[#dce2f0] bg-white pl-11 text-[15px] text-[#1c284a] shadow-none placeholder:text-[#98a2b8] focus-visible:border-[#4555da] focus-visible:ring-[#4555da]/20"
                    :aria-invalid="Boolean(errors.email)"
                />
            </div>
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <Label for="password" class="text-sm font-semibold text-[#192443]">
                    Kata Sandi
                </Label>
                <TextLink
                    v-if="canResetPassword"
                    :href="request()"
                    :tabindex="5"
                    class="text-xs font-semibold text-[#3544cc] hover:text-[#2633a5] hover:underline sm:text-sm"
                >
                    Lupa kata sandi?
                </TextLink>
            </div>
            <div class="relative">
                <LockKeyhole
                    aria-hidden="true"
                    class="pointer-events-none absolute top-1/2 left-4 z-10 size-[18px] -translate-y-1/2 text-[#7885a5]"
                />
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi"
                    class="h-12 rounded-xl border-[#dce2f0] bg-white pl-11 pr-12 text-[15px] text-[#1c284a] shadow-none placeholder:text-[#98a2b8] focus-visible:border-[#4555da] focus-visible:ring-[#4555da]/20"
                    :aria-invalid="Boolean(errors.password)"
                />
            </div>
            <InputError :message="errors.password" />
        </div>

        <Label
            for="remember"
            class="flex w-fit cursor-pointer items-center gap-3 text-sm font-normal text-[#53617e]"
        >
            <Checkbox
                id="remember"
                name="remember"
                :tabindex="3"
                class="border-[#aeb9d4] data-[state=checked]:border-[#3544cc] data-[state=checked]:bg-[#3544cc]"
            />
            <span>Ingat saya</span>
        </Label>

        <Button
            type="submit"
            :disabled="processing"
            :tabindex="4"
            data-test="login-button"
            class="mt-1 h-12 w-full rounded-xl bg-[#313cc8] text-[15px] font-bold text-white shadow-[0_9px_22px_rgba(49,60,200,0.17)] transition-colors hover:bg-[#2734b1] focus-visible:ring-[#4b57e0]/40 disabled:opacity-70"
        >
            <Spinner v-if="processing" class="mr-1" />
            <template v-if="processing">Sedang masuk...</template>
            <template v-else>
                Masuk ke MENTORA
                <ArrowRight aria-hidden="true" class="ml-1 size-4" />
            </template>
        </Button>

        <div class="relative mt-1 flex items-center gap-3" aria-hidden="true">
            <div class="h-px flex-1 bg-[#e3e7f1]" />
            <span class="text-xs text-[#a0a9bc]">Belum punya akun?</span>
            <div class="h-px flex-1 bg-[#e3e7f1]" />
        </div>

        <div class="text-center text-sm text-[#66738e]">
            Daftar gratis dan mulai jelajahi peluang.
            <TextLink
                :href="register()"
                :tabindex="5"
                class="ml-1 font-bold text-[#3544cc] hover:text-[#2633a5] hover:underline"
            >
                Buat Akun
            </TextLink>
        </div>
    </Form>

    <div class="mt-6 flex items-start justify-center gap-2 text-center text-xs leading-5 text-[#8390a7]">
        <ShieldCheck aria-hidden="true" class="mt-0.5 size-4 shrink-0 text-[#626ed3]" />
        <span>Masuk dengan aman menggunakan autentikasi MENTORA.</span>
    </div>
</template>

<style scoped>
.mentora-login-passkey :deep(button) {
    min-height: 46px;
    border-color: #dce2f0;
    border-radius: 12px;
    color: #293658;
    font-weight: 600;
    background: #fff;
}

.mentora-login-passkey :deep(button:hover:not(:disabled)) {
    border-color: #9ea9e9;
    background: #f5f6ff;
}

.mentora-login-passkey :deep([data-slot='separator']) {
    background: #e3e7f1;
}
</style>
