<?php

declare(strict_types=1);

namespace App\Modules\Identity\Console\Commands;

use App\Modules\Identity\Application\Services\FirstSuperAdminBootstrapper;
use DomainException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

/** CLI-only. This command never exposes a web endpoint for privileged grants. */
final class BootstrapFirstSuperAdmin extends Command
{
    protected $signature = 'mentora:rbac:bootstrap-superadmin
        {--name= : Nama akun khusus SuperAdmin}
        {--email= : Email baru yang dikuasai pemilik sistem}
        {--apply : Buat akun pertama (default: pratinjau saja)}
        {--confirm= : Ketik ulang alamat email secara persis untuk konfirmasi}
        {--allow-production : Izin eksplisit jika dijalankan pada production}';

    protected $description = 'Bootstrap satu akun SuperAdmin pertama; tanpa password bawaan atau upgrade akun Member';

    public function handle(FirstSuperAdminBootstrapper $bootstrapper): int
    {
        $name = trim((string) ($this->option('name') ?? ''));
        $email = Str::lower(trim((string) ($this->option('email') ?? '')));

        if (Str::length($name) < 3 || Str::length($name) > 255) {
            $this->error('Nama harus berisi 3–255 karakter.');
            return self::FAILURE;
        }

        if (strlen($email) > 254 || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Alamat email tidak valid.');
            return self::FAILURE;
        }

        try {
            $bootstrapper->assertEligible($email);
        } catch (DomainException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        if (! $this->option('apply')) {
            $this->info('PRATINJAU SAJA — tidak ada data akun atau role yang diubah.');
            $this->line("Akun SuperAdmin khusus yang akan dibuat: {$name} <{$email}>.");
            $this->line('Tambahkan --apply --confirm=<email> untuk mengeksekusi.');
            return self::SUCCESS;
        }

        if (! hash_equals($email, (string) ($this->option('confirm') ?? ''))) {
            $this->error('Konfirmasi gagal. --confirm harus sama persis dengan --email.');
            return self::FAILURE;
        }

        if (app()->environment('production')) {
            if (! $this->option('allow-production')) {
                $this->error('Production memerlukan --allow-production.');
                return self::FAILURE;
            }

            if (in_array(config('mail.default'), ['log', 'array'], true)) {
                $this->error('Bootstrap production ditolak: konfigurasi email harus dapat benar-benar mengirim pesan.');
                return self::FAILURE;
            }
        }

        try {
            $user = $bootstrapper->create($name, $email);
        } catch (DomainException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Pembuatan akun gagal; periksa log Laravel. Tidak ada password yang dikeluarkan.');
            return self::FAILURE;
        }

        $this->info('Akun SuperAdmin pertama dibuat. Belum terverifikasi dan belum memiliki password yang diketahui.');
        $this->line('Tidak ada role Member yang otomatis ditambahkan.');

        $notificationError = false;

        try {
            $status = Password::sendResetLink(['email' => $user->email]);

            if ($status === Password::RESET_LINK_SENT) {
                $this->info('Instruksi pembuatan password dikirim melalui mailer yang dikonfigurasi.');
            } else {
                $notificationError = true;
                $this->warn('Email pembuatan password belum berhasil. Gunakan /forgot-password untuk mengirim ulang.');
            }
        } catch (Throwable $exception) {
            report($exception);
            $notificationError = true;
            $this->warn('Pengiriman instruksi password gagal. Gunakan /forgot-password setelah mailer diperbaiki.');
        }

        try {
            $user->sendEmailVerificationNotification();
            $this->info('Notifikasi verifikasi email dibuat/dikirim sesuai konfigurasi mailer.');
        } catch (Throwable $exception) {
            report($exception);
            $notificationError = true;
            $this->warn('Notifikasi verifikasi gagal. Setelah login, kirim ulang melalui /email/verify.');
        }

        if (config('mail.default') === 'log') {
            $this->warn('MAIL_MAILER=log: kedua tautan hanya tersimpan pada storage/logs/laravel.log.');
        }

        $this->line('Urutan aktivasi: buka tautan reset password, login, lalu verifikasi email.');
        $this->line('Jangan pernah membagikan tautan reset password atau verifikasi.');

        // A nonzero code communicates mail trouble; it does NOT undo the
        // already-committed account. Re-running bootstrap will be rejected.
        return $notificationError ? self::FAILURE : self::SUCCESS;
    }
}
