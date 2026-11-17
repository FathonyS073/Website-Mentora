
<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface Scholarship {
    id: number;
    title: string;
    slug: string;
    provider_name: string;
    summary: string | null;
    education_levels: string[];
    funding_type: string;
    location_scope: string;
}

interface PaginatedScholarships {
    items: Scholarship[];
    total: number;
    current_page: number;
    last_page: number;
    per_page: number;
}

const props = defineProps<{
    scholarships: PaginatedScholarships;
    filters: {
        search: string;
        education_level: string;
        location_scope: string;
        funding_type: string;
        field_of_study: string;
    };
}>();

const keyword = ref(props.filters.search ?? '');
const educationLevel = ref(props.filters.education_level ?? '');

function buildParams(page = 1): Record<string, string | number> {
    const params: Record<string, string | number> = {};

    if (keyword.value.trim()) {
        params.search = keyword.value.trim();
    }

    if (educationLevel.value) {
        params.education_level = educationLevel.value;
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

        <section class="bg-mentora-canvas py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <p class="mb-2 text-sm font-semibold text-mentora-primary">
                        MENTORA Scholarship Catalog
                    </p>

                    <h1 class="text-3xl font-bold text-mentora-navy md:text-4xl">
                        Temukan Beasiswa Impianmu
                    </h1>

                    <p class="mt-3 max-w-2xl text-slate-600">
                        Jelajahi peluang beasiswa sesuai jenjang
                        pendidikan dan tujuan studimu.
                    </p>
                </div>

                <form
                    class="mb-8 grid gap-3 rounded-2xl border
                           border-mentora-border bg-white p-5
                           shadow-sm md:grid-cols-[1fr_200px_auto]"
                    @submit.prevent="submitSearch"
                >
                    <label class="sr-only" for="scholarship-search">
                        Kata kunci beasiswa
                    </label>

                    <input
                        id="scholarship-search"
                        v-model="keyword"
                        type="search"
                        placeholder="Cari nama beasiswa atau penyelenggara..."
                        maxlength="150"
                        class="w-full rounded-xl border border-mentora-border
                               px-4 py-3 text-sm outline-none
                               focus:border-mentora-primary"
                    />

                    <label class="sr-only" for="scholarship-level">
                        Jenjang pendidikan
                    </label>

                    <select
                        id="scholarship-level"
                        v-model="educationLevel"
                        class="rounded-xl border border-mentora-border
                               px-4 py-3 text-sm"
                    >
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

                    <button
                        type="submit"
                        class="rounded-xl bg-mentora-primary px-6 py-3
                               text-sm font-semibold text-white
                               transition hover:bg-mentora-primary-hover"
                    >
                        Cari Beasiswa
                    </button>
                </form>

                <p class="mb-5 text-sm text-slate-600">
                    {{ scholarships.total }} beasiswa ditemukan
                </p>

                <div
                    v-if="scholarships.items.length === 0"
                    class="rounded-2xl border border-mentora-border
                           bg-white px-6 py-16 text-center shadow-sm"
                >
                    <h2 class="text-xl font-bold text-mentora-navy">
                        Belum Ada Beasiswa yang Ditampilkan
                    </h2>

                    <p class="mx-auto mt-3 max-w-lg text-sm text-slate-600">
                        Belum ada informasi beasiswa terverifikasi
                        yang sesuai dengan pencarian saat ini.
                        Silakan mencoba kata kunci atau jenjang lainnya.
                    </p>
                </div>

                <div
                    v-else
                    class="grid gap-5 md:grid-cols-2 xl:grid-cols-3"
                >
                    <article
                        v-for="item in scholarships.items"
                        :key="item.id"
                        class="rounded-2xl border border-mentora-border
                               bg-white p-6 shadow-sm"
                    >
                        <p class="text-xs font-semibold text-mentora-primary">
                            {{ item.provider_name }}
                        </p>

                        <h2 class="mt-2 text-lg font-bold text-mentora-navy">
                            {{ item.title }}
                        </h2>

                        <p class="mt-3 text-sm text-slate-600">
                            {{ item.summary || 'Informasi program tersedia.' }}
                        </p>

                        <p class="mt-4 text-xs text-slate-500">
                            Jenjang:
                            {{ item.education_levels.join(', ') }}
                        </p>
                    </article>
                </div>

                <div
                    v-if="scholarships.last_page > 1"
                    class="mt-8 flex items-center justify-center gap-4"
                >
                    <button
                        type="button"
                        :disabled="scholarships.current_page <= 1"
                        class="rounded-lg border px-4 py-2 text-sm
                               disabled:cursor-not-allowed disabled:opacity-40"
                        @click="changePage(scholarships.current_page - 1)"
                    >
                        Sebelumnya
                    </button>

                    <span class="text-sm text-slate-600">
                        {{ scholarships.current_page }}
                        / {{ scholarships.last_page }}
                    </span>

                    <button
                        type="button"
                        :disabled="
                            scholarships.current_page >= scholarships.last_page
                        "
                        class="rounded-lg border px-4 py-2 text-sm
                               disabled:cursor-not-allowed disabled:opacity-40"
                        @click="changePage(scholarships.current_page + 1)"
                    >
                        Berikutnya
                    </button>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
