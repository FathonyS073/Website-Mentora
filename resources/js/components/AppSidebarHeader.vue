
<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="sticky top-0 z-20 flex h-16 shrink-0
               items-center justify-between
               border-b border-mentora-border
               bg-white/95 px-4 backdrop-blur
               sm:px-6"
    >
        <div class="flex min-w-0 items-center gap-3">
            <SidebarTrigger
                class="-ml-1 shrink-0 rounded-lg
                       text-mentora-navy
                       hover:bg-mentora-lavender
                       hover:text-mentora-primary"
            />

            <div
                class="h-6 w-px shrink-0 bg-mentora-border"
                aria-hidden="true"
            />

            <Breadcrumbs
                v-if="breadcrumbs.length > 0"
                :breadcrumbs="breadcrumbs"
            />

            <span
                v-else
                class="truncate text-sm font-semibold
                       text-mentora-navy"
            >
                Beranda
            </span>
        </div>

        <span
            class="hidden text-xs font-medium
                   text-mentora-muted sm:inline"
        >
            Platform Pendamping Studi &amp; Beasiswa
        </span>
    </header>
</template>
