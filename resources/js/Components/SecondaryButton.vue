<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="tw-inline-flex tw-items-center tw-rounded-md tw-border tw-border-gray-300 tw-bg-white tw-px-4 tw-py-2 tw-text-xs tw-font-semibold tw-uppercase tw-tracking-widest tw-text-gray-700 tw-shadow-sm tw-transition tw-duration-150 tw-ease-in-out tw-hover:bg-gray-50 tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-indigo-500 tw-focus:ring-offset-2 tw-disabled:opacity-25"
    >
        <slot />
    </button>
</template>
