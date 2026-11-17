
<script setup lang="ts">
import { computed } from 'vue';

type ButtonVariant = 'primary' | 'secondary' | 'outline';

const props = withDefaults(
    defineProps<{
        variant?: ButtonVariant;
        type?: 'button' | 'submit' | 'reset';
        disabled?: boolean;
        loading?: boolean;
    }>(),
    {
        variant: 'primary',
        type: 'button',
        disabled: false,
        loading: false,
    },
);

const variants: Record<ButtonVariant, string> = {
    primary:
        'bg-mentora-primary text-white hover:bg-mentora-primary-hover',

    secondary:
        'bg-mentora-lavender text-mentora-primary hover:bg-mentora-canvas',

    outline:
        'border border-mentora-border bg-white text-mentora-navy hover:bg-mentora-canvas',
};

const variantClasses = computed(() => variants[props.variant]);
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :aria-busy="loading"
        :class="variantClasses"
        class="inline-flex min-h-11 items-center justify-center gap-2
               rounded-xl px-5 py-2.5 text-sm font-semibold
               transition-colors duration-150
               focus-visible:outline-none focus-visible:ring-2
               focus-visible:ring-mentora-primary
               focus-visible:ring-offset-2
               disabled:cursor-not-allowed disabled:opacity-50"
    >
        <span
            v-if="loading"
            class="size-4 animate-spin rounded-full border-2
                   border-current border-t-transparent"
            aria-hidden="true"
        />

        <slot />
    </button>
</template>
