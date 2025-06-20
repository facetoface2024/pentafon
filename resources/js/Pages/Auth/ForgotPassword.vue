<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Olvidaste tu Contraseña" />

        <div class="tw-mb-4 tw-text-sm tw-text-gray-600">
            ¿Olvidaste tu contraseña? No hay problema. Solo dinos tu dirección
            de correo electrónico y te enviaremos un enlace para restablecer tu
            contraseña, que te permitirá elegir una nueva.
        </div>

        <div
            v-if="status"
            class="tw-mb-4 tw-text-sm tw-font-medium tw-text-green-600"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo Electrónico" />

                <TextInput
                    id="email"
                    type="email"
                    class="tw-mt-1 tw-block tw-w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="tw-mt-2" :message="form.errors.email" />
            </div>

            <div class="tw-mt-4 tw-flex tw-items-center tw-justify-end">
                <PrimaryButton
                    :class="{ 'tw-opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Enviar Enlace para Restablecer Contraseña
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
