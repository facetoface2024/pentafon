<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="tw-flex tw-min-h-screen tw-flex-col tw-items-center tw-bg-gray-100 tw-pt-6 sm:tw-justify-center sm:tw-pt-0"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="tw-h-20 tw-w-20 tw-fill-current tw-text-gray-500" />
            </Link>
        </div>

        <div
            class="tw-mt-6 tw-w-full tw-overflow-hidden tw-bg-white tw-px-6 tw-py-4 tw-shadow-md sm:tw-max-w-md sm:tw-rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
