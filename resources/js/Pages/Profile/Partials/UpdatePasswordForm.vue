<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="tw-text-lg tw-font-medium tw-text-gray-900">
                Actualizar Contraseña
            </h2>

            <p class="tw-mt-1 tw-text-sm tw-text-gray-600">
                Asegúrate de que tu cuenta esté utilizando una contraseña larga y aleatoria para mantenerla segura.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="tw-mt-6 tw-space-y-6">
            <div>
                <InputLabel for="current_password" value="Contraseña Actual" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="tw-mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="Nueva Contraseña" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="tw-mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Contraseña"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="tw-mt-1 tw-block tw-w-full"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="tw-mt-2"
                />
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
