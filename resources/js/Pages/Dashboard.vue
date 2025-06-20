<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="tw-text-xl tw-font-semibold tw-leading-tight tw-text-gray-800"
            >
                Dashboard
            </h2>

        </template>

        <div class="tw-py-12">
            <div class="tw-mx-auto tw-max-w-7xl sm:tw-px-6 lg:tw-px-8">
                <div
                    class="tw-overflow-hidden tw-bg-white tw-shadow-sm sm:tw-rounded-lg"
                >
                    <div class="tw-p-6 tw-text-gray-900">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
