
<script setup lang="ts">
withDefaults(
    defineProps<{
        padding?: 'normal' | 'compact';
    }>(),
    {
        padding: 'normal',
    },
);
</script>

<template>
    <div
        class="rounded-mentora-card border border-mentora-border
               bg-mentora-white shadow-mentora-card"
        :class="padding === 'normal' ? 'p-6' : 'p-4'"
    >
        <slot />
    </div>
</template>
