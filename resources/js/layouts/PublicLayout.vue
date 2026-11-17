<script setup lang="ts">
import PublicFooter from '@/components/mentora/PublicFooter.vue';
import PublicNavbar from '@/components/mentora/PublicNavbar.vue';
</script>

<template>
    <div class="min-h-screen bg-mentora-canvas font-sans text-mentora-navy antialiased">
        <PublicNavbar />
        <main id="main-content" class="min-h-[50vh]">
            <slot />
        </main>
        <PublicFooter />
    </div>
</template>
