<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="tw-block tw-w-full tw-px-4 tw-py-2 tw-text-start tw-text-sm tw-leading-5 tw-text-gray-700 tw-transition tw-duration-150 tw-ease-in-out tw-hover:bg-gray-100 tw-focus:bg-gray-100 tw-focus:tw-outline-none"
    >
        <slot />
    </Link>
</template>
