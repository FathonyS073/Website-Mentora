
<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowUpRight,
    CalendarDays,
    CheckCircle2,
    ClipboardList,
    FileText,
    Gift,
    GraduationCap,
    MapPin,
    Wallet,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';

/**
 * Representasi data detail beasiswa.
 * Data berasal dari ScholarshipDetailController melalui Inertia.
 */
interface Scholarship {
    id: number;
    title: string;
    slug: string;
    provider_name: string;
    summary: string | null;
    description: string | null;
    education_levels: string[];
    fields_of_study: string[] | null;
    location_scope: string;
    funding_type: string;
    opens_at: string | null;
    closes_at: string | null;
    requirements: string[] | null;
    benefits: string[] | null;
    required_documents: string[] | null;
    source_url: string | null;
    application_url: string | null;
}

const props = defineProps<{
    scholarship: Scholarship;
    preview?: boolean;
}>();

/**
 * Mengonversi kode database menjadi label yang
 * mudah dipahami pengunjung MENTORA.
 */
const locationLabel = computed(() => {
    const labels: Record<string, string> = {
        indonesia: 'Dalam Negeri',
        international: 'Luar Negeri',
    };

    return labels[props.scholarship.location_scope]
        ?? props.scholarship.location_scope;
});

const fundingLabel = computed(() => {
    const labels: Record<string, string> = {
        fully_funded: 'Fully Funded',
        partially_funded: 'Partially Funded',
    };

    return labels[props.scholarship.funding_type]
        ?? props.scholarship.funding_type;
});

const fieldLabels: Record<string, string> = {
    'sains-teknologi': 'Sains & Teknologi',
    'sosial-humaniora': 'Sosial & Humaniora',
    kesehatan: 'Kesehatan',
    lainnya: 'Lainnya',
};

const studyFields = computed(() =>
    (props.scholarship.fields_of_study ?? []).map(
        (field) => fieldLabels[field] ?? field
    )
);

/**
 * Membaca tanggal dari format YYYY-MM-DD atau
 * ISO datetime yang dikirim Eloquent.
 */
function datePart(value: string | null): string | null {
    const match = value?.match(/^\d{4}-\d{2}-\d{2}/);
    return match ? match[0] : null;
}

function formatDate(value: string | null): string {
    const part = datePart(value);

    if (!part) {
        return 'Belum diinformasikan';
    }

    const [year, month, day] = part.split('-').map(Number);
    const date = new Date(year, month - 1, day);

    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

/**
 * Status ini dihitung dari tanggal pada database,
 * bukan konfirmasi real-time dari penyelenggara.
 */
const registrationStage = computed(() => {
    const opens = datePart(props.scholarship.opens_at);
    const closes = datePart(props.scholarship.closes_at);

    const today = new Date().toLocaleDateString('sv-SE', {
        timeZone: 'Asia/Jakarta',
    });

    if (closes && closes < today) {
        return 'closed';
    }

    if (opens && opens > today) {
        return 'upcoming';
    }

    if (opens && closes) {
        return 'scheduled';
    }

    return 'unknown';
});

const registrationLabel = computed(() => {
    const labels: Record<string, string> = {
        closed: 'Periode telah berakhir',
        upcoming: 'Belum masuk periode',
        scheduled: 'Dalam periode terjadwal',
        unknown: 'Jadwal belum lengkap',
    };

    return labels[registrationStage.value];
});

const registrationBadgeClass = computed(() => {
    switch (registrationStage.value) {
        case 'closed':
            return 'bg-slate-100 text-slate-700';
        case 'upcoming':
            return 'bg-amber-50 text-amber-700';
        case 'scheduled':
            return 'bg-emerald-50 text-emerald-700';
        default:
            return 'bg-slate-100 text-slate-600';
    }
});

/**
 * Hanya mengizinkan URL HTTPS atau HTTP untuk
 * tautan keluar dari halaman MENTORA.
 */
function safeExternalUrl(value: string | null): string | null {
    if (!value) {
        return null;
    }

    try {
        const url = new URL(value);

        return ['https:', 'http:'].includes(url.protocol)
            ? url.toString()
            : null;
    } catch {
        return null;
    }
}

const sourceUrl = computed(() =>
    safeExternalUrl(props.scholarship.source_url)
);

const applicationUrl = computed(() =>
    safeExternalUrl(props.scholarship.application_url)
);
</script>

<template>
    <PublicLayout>
        <Head :title="`${scholarship.title} - MENTORA`" />

        <section class="bg-mentora-canvas py-10 lg:py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    v-if="preview"
                    role="note"
                    class="mb-6 rounded-xl border border-amber-200
                        bg-amber-50 px-5 py-4 text-sm
                        font-medium text-amber-900"
                >
                    MODE PREVIEW — Seluruh informasi di halaman ini
                    adalah data contoh untuk pengujian desain MENTORA,
                    bukan program beasiswa resmi.
                </div>

                <!-- Navigasi kembali ke halaman katalog -->
                <Link
                    href="/beasiswa"
                    class="inline-flex items-center gap-2 text-sm
                           font-semibold text-mentora-primary
                           hover:underline"
                >
                    <ArrowLeft :size="17" aria-hidden="true" />
                    Kembali ke Katalog Beasiswa
                </Link>

                <!-- Header utama program beasiswa -->
                <header
                    class="mt-6 rounded-2xl border border-mentora-border
                           bg-white p-6 shadow-sm md:p-8"
                >
                    <div
                        class="flex flex-wrap items-center
                               justify-between gap-3"
                    >
                        <span
                            class="text-xs font-bold uppercase
                                   tracking-wider text-mentora-primary"
                        >
                            MENTORA Scholarship Information
                        </span>

                       
                        <!-- Badge untuk beasiswa asli yang sudah diverifikasi -->
                        <span
                            v-if="!preview"
                            class="inline-flex items-center gap-1.5
                                rounded-full bg-emerald-50 px-3
                                py-1.5 text-xs font-semibold
                                text-emerald-700"
                        >
                            <CheckCircle2 :size="14" aria-hidden="true" />
                            Terverifikasi
                        </span>

                        <!-- Badge khusus untuk halaman preview -->
                        <span
                            v-else
                            class="inline-flex items-center rounded-full
                                bg-amber-50 px-3 py-1.5
                                text-xs font-semibold text-amber-700"
                        >
                            Data Contoh
                        </span>

                    </div>

                    <p
                        class="mt-5 text-sm font-semibold
                               text-mentora-primary"
                    >
                        {{ scholarship.provider_name }}
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold leading-tight
                               text-mentora-navy sm:text-3xl
                               lg:text-4xl"
                    >
                        {{ scholarship.title }}
                    </h1>

                    <p
                        class="mt-4 max-w-3xl leading-7
                               text-slate-600"
                    >
                        {{
                            scholarship.summary
                                || 'Ringkasan program belum tersedia.'
                        }}
                    </p>

                    <!-- Metadata utama beasiswa -->
                    <div
                        class="mt-7 grid gap-3 sm:grid-cols-2
                               lg:grid-cols-3"
                    >
                        <div
                            class="flex items-start gap-3 rounded-xl
                                   bg-slate-50 p-4"
                        >
                            <GraduationCap
                                :size="20"
                                class="shrink-0 text-mentora-primary"
                                aria-hidden="true"
                            />
                            <div>
                                <p class="text-xs text-slate-500">
                                    Jenjang Pendidikan
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold
                                           text-mentora-navy"
                                >
                                    {{ scholarship.education_levels.join(', ') }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 rounded-xl
                                   bg-slate-50 p-4"
                        >
                            <MapPin
                                :size="20"
                                class="shrink-0 text-mentora-primary"
                                aria-hidden="true"
                            />
                            <div>
                                <p class="text-xs text-slate-500">
                                    Lokasi Studi
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold
                                           text-mentora-navy"
                                >
                                    {{ locationLabel }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 rounded-xl
                                   bg-slate-50 p-4"
                        >
                            <Wallet
                                :size="20"
                                class="shrink-0 text-mentora-primary"
                                aria-hidden="true"
                            />
                            <div>
                                <p class="text-xs text-slate-500">
                                    Jenis Pendanaan
                                </p>
                                <p
                                    class="mt-1 text-sm font-semibold
                                           text-mentora-navy"
                                >
                                    {{ fundingLabel }}
                                </p>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Konten dan sidebar pendaftaran -->
                <div
                    class="mt-6 grid items-start gap-6
                           lg:grid-cols-[minmax(0,1fr)_340px]"
                >

                    <!-- Kolom kiri: informasi lengkap -->
                    <main class="min-w-0 space-y-6">

                        <!-- Deskripsi program -->
                        <section
                            class="rounded-2xl border
                                   border-mentora-border bg-white
                                   p-6 shadow-sm md:p-8"
                        >
                            <h2
                                class="text-xl font-bold
                                       text-mentora-navy"
                            >
                                Tentang Program Beasiswa
                            </h2>

                            <p
                                class="mt-4 whitespace-pre-line
                                       break-words leading-7
                                       text-slate-600"
                            >
                                {{
                                    scholarship.description
                                        || 'Deskripsi lengkap belum tersedia. '
                                        + 'Silakan periksa sumber resmi.'
                                }}
                            </p>
                        </section>

                        <!-- Persyaratan pendaftaran -->
                        <section
                            class="rounded-2xl border
                                   border-mentora-border bg-white
                                   p-6 shadow-sm md:p-8"
                        >
                            <div class="flex items-center gap-3">
                                <ClipboardList
                                    :size="22"
                                    class="text-mentora-primary"
                                    aria-hidden="true"
                                />
                                <h2
                                    class="text-xl font-bold
                                           text-mentora-navy"
                                >
                                    Persyaratan Pendaftaran
                                </h2>
                            </div>

                            <ul
                                v-if="scholarship.requirements?.length"
                                class="mt-5 space-y-3"
                            >
                                <li
                                    v-for="(requirement, index) in scholarship.requirements"
                                    :key="index"
                                    class="flex items-start gap-3
                                           text-sm leading-6
                                           text-slate-600"
                                >
                                    <CheckCircle2
                                        :size="18"
                                        class="mt-1 shrink-0
                                               text-mentora-primary"
                                        aria-hidden="true"
                                    />
                                    <span>{{ requirement }}</span>
                                </li>
                            </ul>

                            <p
                                v-else
                                class="mt-4 text-sm text-slate-500"
                            >
                                Persyaratan belum tersedia.
                                Periksa informasi dari sumber resmi.
                            </p>
                        </section>

                        <!-- Manfaat program -->
                        <section
                            class="rounded-2xl border
                                   border-mentora-border bg-white
                                   p-6 shadow-sm md:p-8"
                        >
                            <div class="flex items-center gap-3">
                                <Gift
                                    :size="22"
                                    class="text-mentora-primary"
                                    aria-hidden="true"
                                />
                                <h2
                                    class="text-xl font-bold
                                           text-mentora-navy"
                                >
                                    Manfaat Beasiswa
                                </h2>
                            </div>

                            <ul
                                v-if="scholarship.benefits?.length"
                                class="mt-5 space-y-3"
                            >
                                <li
                                    v-for="(benefit, index) in scholarship.benefits"
                                    :key="index"
                                    class="flex items-start gap-3
                                           text-sm leading-6
                                           text-slate-600"
                                >
                                    <CheckCircle2
                                        :size="18"
                                        class="mt-1 shrink-0
                                               text-mentora-primary"
                                        aria-hidden="true"
                                    />
                                    <span>{{ benefit }}</span>
                                </li>
                            </ul>

                            <p
                                v-else
                                class="mt-4 text-sm text-slate-500"
                            >
                                Informasi manfaat belum tersedia.
                                Silakan merujuk ke penyelenggara.
                            </p>
                        </section>

                        <!-- Dokumen yang diperlukan -->
                        <section
                            class="rounded-2xl border
                                   border-mentora-border bg-white
                                   p-6 shadow-sm md:p-8"
                        >
                            <div class="flex items-center gap-3">
                                <FileText
                                    :size="22"
                                    class="text-mentora-primary"
                                    aria-hidden="true"
                                />
                                <h2
                                    class="text-xl font-bold
                                           text-mentora-navy"
                                >
                                    Dokumen Pendaftaran
                                </h2>
                            </div>

                            <ul
                                v-if="scholarship.required_documents?.length"
                                class="mt-5 space-y-3"
                            >
                                <li
                                    v-for="(document, index) in scholarship.required_documents"
                                    :key="index"
                                    class="flex items-start gap-3
                                           text-sm leading-6
                                           text-slate-600"
                                >
                                    <CheckCircle2
                                        :size="18"
                                        class="mt-1 shrink-0
                                               text-mentora-primary"
                                        aria-hidden="true"
                                    />
                                    <span>{{ document }}</span>
                                </li>
                            </ul>

                            <p
                                v-else
                                class="mt-4 text-sm text-slate-500"
                            >
                                Daftar dokumen belum tersedia.
                                Pastikan persyaratan terbaru melalui
                                sumber resmi penyelenggara.
                            </p>
                        </section>

                        <!-- Bidang studi -->
                        <section
                            v-if="studyFields.length"
                            class="rounded-2xl border
                                   border-mentora-border bg-white
                                   p-6 shadow-sm md:p-8"
                        >
                            <h2
                                class="text-xl font-bold
                                       text-mentora-navy"
                            >
                                Bidang Studi
                            </h2>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span
                                    v-for="field in studyFields"
                                    :key="field"
                                    class="rounded-full bg-indigo-50
                                           px-3 py-1.5 text-sm
                                           font-medium text-mentora-primary"
                                >
                                    {{ field }}
                                </span>
                            </div>
                        </section>
                    </main>

                    <!-- Kolom kanan: informasi dan aksi -->
                    <aside class="space-y-5 lg:sticky lg:top-24">
                        <section
                            class="rounded-2xl border
                                   border-mentora-border bg-white
                                   p-6 shadow-sm"
                        >
                            <h2
                                class="text-lg font-bold
                                       text-mentora-navy"
                            >
                                Informasi Pendaftaran
                            </h2>

                            <div class="mt-5">
                                <span
                                    class="inline-flex rounded-full
                                           px-3 py-1.5 text-xs
                                           font-semibold"
                                    :class="registrationBadgeClass"
                                >
                                    {{ registrationLabel }}
                                </span>
                            </div>

                            <div class="mt-6 space-y-5">
                                <div class="flex items-start gap-3">
                                    <CalendarDays
                                        :size="19"
                                        class="mt-0.5 shrink-0
                                               text-mentora-primary"
                                        aria-hidden="true"
                                    />
                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Pembukaan Pendaftaran
                                        </p>
                                        <p
                                            class="mt-1 text-sm
                                                   font-semibold
                                                   text-mentora-navy"
                                        >
                                            {{ formatDate(scholarship.opens_at) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <CalendarDays
                                        :size="19"
                                        class="mt-0.5 shrink-0
                                               text-mentora-primary"
                                        aria-hidden="true"
                                    />
                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Batas Pendaftaran
                                        </p>
                                        <p
                                            class="mt-1 text-sm
                                                   font-semibold
                                                   text-mentora-navy"
                                        >
                                            {{ formatDate(scholarship.closes_at) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <p
                                class="mt-5 rounded-xl bg-slate-50
                                       p-3 text-xs leading-5
                                       text-slate-500"
                            >
                                Status berdasarkan tanggal yang
                                tercatat di MENTORA. Konfirmasi
                                jadwal dan ketersediaan pendaftaran
                                melalui sumber resmi.
                            </p>

                            <div class="mt-6 space-y-3">
                                <!-- Tautan aplikasi resmi -->
                                <a
                                    v-if="applicationUrl"
                                    :href="applicationUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex w-full items-center
                                           justify-center gap-2 rounded-xl
                                           bg-mentora-primary px-4 py-3
                                           text-sm font-bold text-white
                                           transition hover:opacity-90"
                                >
                                    Halaman Pendaftaran
                                    <ArrowUpRight
                                        :size="17"
                                        aria-hidden="true"
                                    />
                                </a>

                                <!-- Tautan sumber informasi -->

                                <!-- Website resmi informasi beasiswa -->
                                <a
                                    v-if="sourceUrl"
                                    :href="sourceUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex w-full items-center justify-between gap-3
                                        rounded-xl border border-mentora-border
                                        bg-white px-4 py-3 text-mentora-navy
                                        transition hover:border-mentora-primary
                                        hover:bg-indigo-50"
                                >
                                    <div class="min-w-0 text-left">
                                        <p class="text-sm font-semibold">
                                            {{
                                                preview
                                                    ? 'Website Contoh (Preview)'
                                                    : 'Website Resmi Beasiswa'
                                            }}
                                        </p>

                                        <p
                                            class="mt-1 break-all text-xs leading-5
                                                text-slate-500"
                                        >
                                            {{ sourceUrl }}
                                        </p>
                                    </div>

                                    <ArrowUpRight
                                        :size="18"
                                        class="shrink-0 text-mentora-primary"
                                        aria-hidden="true"
                                    />
                                </a>


                                <p
                                    v-if="!applicationUrl && !sourceUrl"
                                    class="text-sm text-slate-500"
                                >
                                    Tautan pendaftaran dan sumber
                                    resmi belum tersedia.
                                </p>
                            </div>
                        </section>

                        <div
                            class="rounded-2xl border
                                   border-mentora-border
                                   bg-white p-5"
                        >
                            <p
                                class="text-sm font-semibold
                                       text-mentora-navy"
                            >
                                Informasi Penting
                            </p>
                            <p
                                class="mt-2 text-xs leading-6
                                       text-slate-500"
                            >
                                MENTORA membantu menemukan informasi
                                beasiswa. Keputusan penerimaan,
                                perubahan jadwal, dan proses
                                pendaftaran merupakan kewenangan
                                penyelenggara resmi.
                            </p>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
