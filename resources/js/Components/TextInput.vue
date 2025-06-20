<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="tw-rounded-md tw-border-gray-300 tw-shadow-sm tw-focus:border-indigo-500 tw-focus:ring-indigo-500"
        v-model="model"
        ref="input"
    />
</template>
