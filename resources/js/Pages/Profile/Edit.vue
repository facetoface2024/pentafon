<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="tw-text-xl tw-font-semibold tw-leading-tight tw-text-gray-800"
            >
                Datos personales
            </h2>
        </template>

        <div class="tw-py-12">
            <div class="tw-mx-auto tw-max-w-7xl tw-space-y-6 sm:tw-px-6 lg:tw-px-8">
                <div
                    class="tw-bg-white tw-p-4 tw-shadow sm:tw-rounded-lg sm:tw-p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="tw-max-w-xl"
                    />
                </div>

                <div
                    class="tw-bg-white tw-p-4 tw-shadow sm:tw-rounded-lg sm:tw-p-8"
                >
                    <UpdatePasswordForm class="tw-max-w-xl" />
                </div>

                <div
                    class="tw-bg-white tw-p-4 tw-shadow sm:tw-rounded-lg sm:tw-p-8"
                >
                    <DeleteUserForm class="tw-max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
