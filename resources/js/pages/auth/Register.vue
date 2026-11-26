<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ArrowRight, GraduationCap, LockKeyhole, Mail, ShieldCheck, UserRound, UsersRound } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

// Tetap menerima properti validasi password dari Laravel/Fortify.
defineProps<{
    passwordRules: string;
}>();

// Judul dirender di dalam halaman agar kata MENTORA bisa diberi aksen.
// AuthSimpleLayout tetap menampilkan logo dan kerangka kartu.
defineOptions({
    layout: {
        title: '',
        description: '',
    },
});
</script>

<template>
    <Head title="Buat Akun MENTORA" />

    <div class="mb-7 text-center">
        <h1
            class="text-[28px] font-extrabold leading-tight tracking-[-0.035em] text-[#111936] sm:text-[32px]"
        >
            Buat Akun <span class="text-[#313cc8]">MENTORA</span>
        </h1>
        <p
            class="mx-auto mt-3 max-w-[450px] text-sm leading-6 text-[#64708c] sm:text-[15px]"
        >
            Bergabung sebagai pelajar, mahasiswa, orang tua, atau wali untuk
            mempersiapkan perjalanan pendidikan bersama MENTORA.
        </p>
    </div>

    <!-- Endpoint POST Fortify tetap sama. Backend hanya menerima dua jenis akun publik. -->
    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <!-- Pilihan jenis akun publik: role internal tidak boleh ditawarkan di sini. -->
        <fieldset class="grid gap-3">
            <legend class="mb-2 text-sm font-semibold text-[#192443]">
                Daftar Sebagai <span class="text-[#e34a66]">*</span>
            </legend>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        name="account_type"
                        value="member"
                        checked
                        required
                        :tabindex="1"
                        class="peer sr-only"
                    />
                    <span class="flex min-h-[116px] flex-col rounded-xl border border-[#dce2f0] bg-white p-4 transition-colors peer-checked:border-[#313cc8] peer-checked:bg-[#f4f6ff] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#313cc8]">
                        <span class="mb-2 flex items-center gap-2 text-sm font-bold text-[#192443]">
                            <GraduationCap aria-hidden="true" class="size-[19px] text-[#313cc8]" />
                            Pelajar / Mahasiswa
                        </span>
                        <span class="text-xs leading-5 text-[#65728f]">
                            Mencari beasiswa, menyiapkan persyaratan, dan mengelola pendaftaran.
                        </span>
                    </span>
                </label>
                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        name="account_type"
                        value="guardian"
                        required
                        :tabindex="1"
                        class="peer sr-only"
                    />
                    <span class="flex min-h-[116px] flex-col rounded-xl border border-[#dce2f0] bg-white p-4 transition-colors peer-checked:border-[#313cc8] peer-checked:bg-[#f4f6ff] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#313cc8]">
                        <span class="mb-2 flex items-center gap-2 text-sm font-bold text-[#192443]">
                            <UsersRound aria-hidden="true" class="size-[19px] text-[#313cc8]" />
                            Orang Tua / Wali
                        </span>
                        <span class="text-xs leading-5 text-[#65728f]">
                            Mendampingi Member setelah mendapatkan izin yang sesuai.
                        </span>
                    </span>
                </label>
            </div>
            <InputError :message="errors.account_type" />
            <p class="text-xs leading-5 text-[#8190aa]">
                Jenis akun menentukan fitur yang dapat digunakan. Akun staf MENTORA dibuat melalui undangan resmi.
            </p>
        </fieldset>

        <!-- Nama lengkap -->
        <div class="grid gap-2">
            <Label for="name" class="text-sm font-semibold text-[#192443]">
                Nama Lengkap
            </Label>
            <div class="relative">
                <UserRound
                    aria-hidden="true"
                    class="pointer-events-none absolute top-1/2 left-4 size-[18px] -translate-y-1/2 text-[#7885a5]"
                />
                <Input
                    id="name"
                    type="text"
                    name="name"
                    required
                    v-focus
                    :tabindex="2"
                    autocomplete="name"
                    maxlength="255"
                    placeholder="Masukkan nama lengkap"
                    class="h-12 rounded-xl border-[#dce2f0] bg-white pl-11 text-[15px] text-[#1c284a] shadow-none placeholder:text-[#98a2b8] focus-visible:border-[#4555da] focus-visible:ring-[#4555da]/20"
                    :aria-invalid="Boolean(errors.name)"
                />
            </div>
            <InputError :message="errors.name" />
        </div>

        <!-- Email -->
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
                    :tabindex="3"
                    autocomplete="email"
                    maxlength="255"
                    placeholder="Masukkan alamat email"
                    class="h-12 rounded-xl border-[#dce2f0] bg-white pl-11 text-[15px] text-[#1c284a] shadow-none placeholder:text-[#98a2b8] focus-visible:border-[#4555da] focus-visible:ring-[#4555da]/20"
                    :aria-invalid="Boolean(errors.email)"
                />
            </div>
            <InputError :message="errors.email" />
        </div>

        <!-- Password memakai komponen bawaan: toggle show/hide tetap bekerja. -->
        <div class="grid gap-2">
            <Label for="password" class="text-sm font-semibold text-[#192443]">
                Kata Sandi
            </Label>
            <div class="relative">
                <LockKeyhole
                    aria-hidden="true"
                    class="pointer-events-none absolute top-1/2 left-4 z-10 size-[18px] -translate-y-1/2 text-[#7885a5]"
                />
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    placeholder="Buat kata sandi yang kuat"
                    :passwordrules="passwordRules"
                    class="h-12 rounded-xl border-[#dce2f0] bg-white pl-11 pr-12 text-[15px] text-[#1c284a] shadow-none placeholder:text-[#98a2b8] focus-visible:border-[#4555da] focus-visible:ring-[#4555da]/20"
                    :aria-invalid="Boolean(errors.password)"
                />
            </div>
            <InputError :message="errors.password" />
        </div>

        <!-- Konfirmasi kata sandi -->
        <div class="grid gap-2">
            <Label
                for="password_confirmation"
                class="text-sm font-semibold text-[#192443]"
            >
                Konfirmasi Kata Sandi
            </Label>
            <div class="relative">
                <LockKeyhole
                    aria-hidden="true"
                    class="pointer-events-none absolute top-1/2 left-4 z-10 size-[18px] -translate-y-1/2 text-[#7885a5]"
                />
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    :tabindex="5"
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                    :passwordrules="passwordRules"
                    class="h-12 rounded-xl border-[#dce2f0] bg-white pl-11 pr-12 text-[15px] text-[#1c284a] shadow-none placeholder:text-[#98a2b8] focus-visible:border-[#4555da] focus-visible:ring-[#4555da]/20"
                    :aria-invalid="Boolean(errors.password_confirmation)"
                />
            </div>
            <InputError :message="errors.password_confirmation" />
        </div>

        <!-- Jangan tambahkan checkbox persetujuan legal palsu sebelum ada dokumen dan validasi backend. -->
        <p class="-mt-1 flex items-start gap-2 text-xs leading-5 text-[#8190aa]">
            <ShieldCheck
                aria-hidden="true"
                class="mt-0.5 size-4 shrink-0 text-[#626ed3]"
            />
            Informasi ini digunakan untuk membuat dan mengamankan akun MENTORA.
        </p>

        <Button
            type="submit"
            :disabled="processing"
            :tabindex="6"
            data-test="register-user-button"
            class="mt-1 h-12 w-full rounded-xl bg-[#313cc8] text-[15px] font-bold text-white shadow-[0_9px_22px_rgba(49,60,200,0.17)] transition-colors hover:bg-[#2734b1] focus-visible:ring-[#4b57e0]/40 disabled:opacity-70"
        >
            <Spinner v-if="processing" class="mr-1" />
            <template v-if="processing">Sedang mendaftarkan...</template>
            <template v-else>
                Buat Akun Gratis
                <ArrowRight aria-hidden="true" class="ml-1 size-4" />
            </template>
        </Button>

        <div class="mt-1 flex items-center gap-3" aria-hidden="true">
            <div class="h-px flex-1 bg-[#e3e7f1]" />
            <span class="text-xs text-[#a0a9bc]">Sudah punya akun?</span>
            <div class="h-px flex-1 bg-[#e3e7f1]" />
        </div>

        <div class="text-center text-sm text-[#66738e]">
            Masuk untuk melanjutkan perjalananmu.
            <TextLink
                :href="login()"
                :tabindex="7"
                class="ml-1 font-bold text-[#3544cc] hover:text-[#2633a5] hover:underline"
            >
                Masuk
            </TextLink>
        </div>
    </Form>
</template>
