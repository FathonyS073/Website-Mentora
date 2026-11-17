<script setup lang="ts">
withDefaults(
    defineProps<{
        compact?: boolean;
    }>(),
    { compact: false },
);
</script>

<template>
    <span class="inline-flex items-center gap-2.5 text-mentora-navy">
        <!-- Placeholder vector brand mark; replace with the approved logo asset when available. -->
        <svg
            class="size-9 shrink-0"
            viewBox="0 0 48 48"
            fill="none"
            aria-hidden="true"
        >
            <path
                d="M6 36V12.5C6 7.5 11.7 5.1 15.2 8.7L24 18.4V37.5L13.4 25.8V36C13.4 41.8 6 41.8 6 36Z"
                fill="#5364E8"
            />
            <path
                d="M42 36V12.5C42 7.5 36.3 5.1 32.8 8.7L24 18.4V37.5L34.6 25.8V36C34.6 41.8 42 41.8 42 36Z"
                fill="#3035B5"
            />
            <path d="M13.4 25.8L24 37.5V18.4L15.2 8.7" fill="#4149DB" />
        </svg>
        <span
            v-if="!compact"
            class="text-lg font-extrabold tracking-tight sm:text-xl"
        >
            MENTORA
        </span>
    </span>
</template>
