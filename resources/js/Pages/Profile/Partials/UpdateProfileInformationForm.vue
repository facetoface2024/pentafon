<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="tw-text-lg tw-font-medium tw-text-gray-900">
                Información del Perfil
            </h2>

            <p class="tw-mt-1 tw-text-sm tw-text-gray-600">
                Actualiza la información de tu perfil y dirección de correo electrónico.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="tw-mt-6 tw-space-y-6"
        >
            <div>
                <InputLabel for="name" value="Nombre" />

                <TextInput
                    id="name"
                    type="text"
                    class="tw-mt-1 tw-block tw-w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="tw-mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Correo Electrónico" />

                <TextInput
                    id="email"
                    type="email"
                    class="tw-mt-1 tw-block tw-w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="tw-mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="tw-mt-2 tw-text-sm tw-text-gray-800">
                    Tu dirección de correo electrónico no está verificada.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="tw-rounded-md tw-text-sm tw-text-gray-600 tw-underline tw-hover:text-gray-900 tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-indigo-500 tw-focus:ring-offset-2"
                    >
                        Haz clic aquí para reenviar el correo de verificación.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="tw-mt-2 tw-text-sm tw-font-medium tw-text-green-600"
                >
                    Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.
                </div>
            </div>

            <div class="tw-flex tw-items-center tw-gap-4">
                <PrimaryButton :disabled="form.processing">Guardar</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="tw-opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="tw-opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="tw-text-sm tw-text-gray-600"
                    >
                        Guardado.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
