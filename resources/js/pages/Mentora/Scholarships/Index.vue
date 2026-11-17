<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import ScholarshipCard from '@/components/mentora/ScholarshipCard.vue';

interface Scholarship {
    id: number;
    title: string;
    slug: string;
    provider_name: string;
    summary: string | null;
    education_levels: string[];
    funding_type: string;
    location_scope: string;
    opens_at?: string | null;
    closes_at?: string | null;
    source_url?: string | null;
}

interface PaginatedScholarships {
    items: Scholarship[];
    total: number;
    current_page: number;
    last_page: number;
    per_page: number;
}

interface CatalogFilters {
    search: string;
    education_level: string;
    location_scope: string;
    funding_type: string;
    field_of_study: string;
}

const props = defineProps<{
    scholarships: PaginatedScholarships;
    filters: CatalogFilters;
}>();

const keyword = ref(props.filters.search ?? '');
const educationLevel = ref(props.filters.education_level ?? '');
const locationScope = ref(props.filters.location_scope ?? '');
const fundingType = ref(props.filters.funding_type ?? '');
const fieldOfStudy = ref(props.filters.field_of_study ?? '');

// Tetap sinkron jika parameter URL berubah melalui navigasi Inertia.
watch(
    () => props.filters,
    (filters) => {
        keyword.value = filters.search ?? '';
        educationLevel.value = filters.education_level ?? '';
        locationScope.value = filters.location_scope ?? '';
        fundingType.value = filters.funding_type ?? '';
        fieldOfStudy.value = filters.field_of_study ?? '';
    },
);

const controlClass =
    'h-12 w-full rounded-xl border border-mentora-border bg-white px-4 text-sm text-mentora-navy outline-none transition focus:border-mentora-primary focus:ring-2 focus:ring-mentora-lavender';

function buildParams(page = 1): Record<string, string | number> {
    const params: Record<string, string | number> = {};
    const filters = {
        search: keyword.value.trim(),
        education_level: educationLevel.value,
        location_scope: locationScope.value,
        funding_type: fundingType.value,
        field_of_study: fieldOfStudy.value,
    };

    for (const [key, value] of Object.entries(filters)) {
        if (value !== '') {
            params[key] = value;
        }
    }

    if (page > 1) {
        params.page = page;
    }

    return params;
}

function submitSearch(): void {
    router.get('/beasiswa', buildParams(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function changePage(page: number): void {
    if (page < 1 || page > props.scholarships.last_page) {
        return;
    }

    router.get('/beasiswa', buildParams(page), {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <PublicLayout>
        <Head title="Katalog Beasiswa" />

        <section class="bg-mentora-canvas py-10 lg:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <p class="mb-2 text-sm font-semibold text-mentora-primary">
                        MENTORA Scholarship Catalog
                    </p>
                    <h1 class="text-3xl font-bold text-mentora-navy md:text-4xl">
                        Temukan Beasiswa Impianmu
                    </h1>
                    <p class="mt-3 max-w-2xl text-slate-600">
                        Jelajahi peluang beasiswa sesuai jenjang pendidikan dan tujuan studimu.
                    </p>
                </div>

                <!-- Grid konsisten: 1 kolom HP, 2 kolom tablet, 12 kolom desktop. -->
                <form
                    class="mb-8 grid grid-cols-1 items-end gap-x-4 gap-y-5 rounded-2xl border border-mentora-border bg-white p-5 shadow-sm sm:p-6 md:grid-cols-2 lg:grid-cols-12"
                    role="search"
                    @submit.prevent="submitSearch"
                >
                    <!-- Baris desktop pertama: kata kunci (6), jenjang (3), lokasi (3). -->
                    <div class="min-w-0 md:col-span-2 lg:col-span-6">
                        <label for="scholarship-search" class="mb-2 block text-sm font-semibold text-mentora-navy">
                            Cari Beasiswa
                        </label>
                        <input
                            id="scholarship-search"
                            v-model="keyword"
                            type="search"
                            placeholder="Nama beasiswa atau penyelenggara..."
                            maxlength="150"
                            :class="controlClass"
                        />
                    </div>

                    <div class="min-w-0 lg:col-span-3">
                        <label for="scholarship-level" class="mb-2 block text-sm font-semibold text-mentora-navy">
                            Jenjang Pendidikan
                        </label>
                        <select id="scholarship-level" v-model="educationLevel" :class="controlClass">
                            <option value="">Semua Jenjang</option>
                            <option value="SD/MI">SD/MI</option>
                            <option value="SMP/MTs">SMP/MTs</option>
                            <option value="SMA/MA/SMK">SMA/MA/SMK</option>
                            <option value="D3">D3</option>
                            <option value="D4">D4</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>

                    <div class="min-w-0 lg:col-span-3">
                        <label for="catalog-location" class="mb-2 block text-sm font-semibold text-mentora-navy">
                            Lokasi Studi
                        </label>
                        <select id="catalog-location" v-model="locationScope" :class="controlClass">
                            <option value="">Semua Lokasi</option>
                            <option value="indonesia">Dalam Negeri</option>
                            <option value="international">Luar Negeri</option>
                        </select>
                    </div>

                    <!-- Baris desktop kedua: pendanaan (4), bidang (4), tombol (4). -->
                    <div class="min-w-0 lg:col-span-4">
                        <label for="catalog-funding" class="mb-2 block text-sm font-semibold text-mentora-navy">
                            Jenis Pendanaan
                        </label>
                        <select id="catalog-funding" v-model="fundingType" :class="controlClass">
                            <option value="">Semua Pendanaan</option>
                            <option value="fully_funded">Fully Funded</option>
                            <option value="partially_funded">Partially Funded</option>
                        </select>
                    </div>

                    <div class="min-w-0 lg:col-span-4">
                        <label for="catalog-field" class="mb-2 block text-sm font-semibold text-mentora-navy">
                            Bidang Studi
                        </label>
                        <select id="catalog-field" v-model="fieldOfStudy" :class="controlClass">
                            <option value="">Semua Bidang</option>
                            <option value="sains-teknologi">Sains &amp; Teknologi</option>
                            <option value="sosial-humaniora">Sosial &amp; Humaniora</option>
                            <option value="kesehatan">Kesehatan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="min-w-0 md:col-span-2 lg:col-span-4">
                        <button
                            type="submit"
                            class="flex h-12 w-full items-center justify-center rounded-xl bg-mentora-primary px-6 text-sm font-semibold text-white transition hover:bg-mentora-primary-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-mentora-primary"
                        >
                            Cari Beasiswa
                        </button>
                    </div>
                </form>

                <p class="mb-5 text-sm text-slate-600">
                    {{ scholarships.total }} beasiswa ditemukan
                </p>

                <div
                    v-if="scholarships.items.length === 0"
                    class="rounded-2xl border border-mentora-border bg-white px-6 py-16 text-center shadow-sm"
                >
                    <h2 class="text-xl font-bold text-mentora-navy">
                        Belum Ada Beasiswa yang Ditampilkan
                    </h2>
                    <p class="mx-auto mt-3 max-w-lg text-sm text-slate-600">
                        Belum ada informasi beasiswa terverifikasi yang sesuai dengan pencarian saat ini.
                        Silakan mencoba kata kunci atau jenjang lainnya.
                    </p>
                </div>

                <div v-else class="space-y-4" aria-label="Daftar beasiswa">
                    <ScholarshipCard
                        v-for="item in scholarships.items"
                        :key="item.id"
                        :scholarship="item"
                    />
                </div>

                <div
                    v-if="scholarships.last_page > 1"
                    class="mt-8 flex items-center justify-center gap-4"
                >
                    <button
                        type="button"
                        :disabled="scholarships.current_page <= 1"
                        class="rounded-lg border px-4 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-40"
                        @click="changePage(scholarships.current_page - 1)"
                    >
                        Sebelumnya
                    </button>
                    <span class="text-sm text-slate-600">
                        {{ scholarships.current_page }} / {{ scholarships.last_page }}
                    </span>
                    <button
                        type="button"
                        :disabled="scholarships.current_page >= scholarships.last_page"
                        class="rounded-lg border px-4 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-40"
                        @click="changePage(scholarships.current_page + 1)"
                    >
                        Berikutnya
                    </button>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
