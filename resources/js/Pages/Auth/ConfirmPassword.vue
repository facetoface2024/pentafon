<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirmar Contraseña" />

        <div class="tw-mb-4 tw-text-sm tw-text-gray-600">
            Esta es una área segura de la aplicación. Por favor, confirma tu
            contraseña antes de continuar.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                />
                <InputError class="tw-mt-2" :message="form.errors.password" />
            </div>

            <div class="tw-mt-4 tw-flex tw-justify-end">
                <PrimaryButton
                    class="tw-ms-4"
                    :class="{ 'tw-opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Confirmar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
