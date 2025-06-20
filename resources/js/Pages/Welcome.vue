<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import HeaderSection from '@/Components/HeaderSection.vue';
import HeroIntro from '@/Components/HeroIntro.vue';
import AgendaSection from '@/Components/AgendaSection.vue';
import SpeakersSection from '@/Components/SpeakersSection.vue';
import Ejes from '@/Components/Ejes.vue';
import RegisterSection from '@/Components/RegisterSection.vue';
import PrivacyNotice from '@/Components/PrivacyNotice.vue';
import Preloader from '@/Components/Preloader.vue';

const isLoading = ref(true);

const handleLoaded = () => {
    isLoading.value = false;
};

onMounted(() => {
    // También ocultar el preloader si todo está cargado después de 3 segundos
    setTimeout(() => {
        isLoading.value = false;
    }, 3000);
});
</script>

<template>
    <Head title="Innovation Day 2025" />
    <v-app>
        <Preloader :is-loading="isLoading" @loaded="handleLoaded" />
        <HeaderSection />
        <v-main>
            <HeroIntro id="inicio" />
<!--             <AgendaSection id="agenda" />
            <SpeakersSection id="speakers" /> -->
            <Ejes id="ejes" />
            <RegisterSection id="registro" />
        </v-main>
        <PrivacyNotice />
    </v-app>
</template>

<style scoped>
:deep(.v-main) {
    padding-top: 64px;
}
</style>
