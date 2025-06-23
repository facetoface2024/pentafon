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

const scrollToRegistro = () => {
    const element = document.getElementById('registro');
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
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
        <!--    <AgendaSection id="agenda" />
            <SpeakersSection id="speakers" />  -->
            <Ejes id="ejes" />
            <RegisterSection id="registro" />
        </v-main>
        <PrivacyNotice />

        <!-- Botón flotante de fecha y hora -->
        <v-btn
            class="floating-event-btn"
            color="#eb1c2d"
            @click="scrollToRegistro"
            elevation="6"
            rounded="pill"
        >
            <div class="event-btn-content">
                <div class="event-date">📅 10 Julio 2025</div>
                <div class="event-time">🕔 5:00 PM</div>
            </div>
        </v-btn>
    </v-app>
</template>

<style scoped>
:deep(.v-main) {
    padding-top: 64px;
}

.floating-event-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(235, 28, 45, 0.3);
    border: none;
    padding: 15px 20px !important;
    height: auto !important;
    min-height: 75px;
}

.floating-event-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(235, 28, 45, 0.4);
}

.event-btn-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    line-height: 1.2;
}

.event-date {
    font-size: 18px;
    font-weight: 600;
    color: white;
    margin-bottom: 2px;
}

.event-time {
    font-size: 15px;
    font-weight: 500;
    color: white;
    opacity: 0.95;
}

/* Estilos para móvil */
@media (max-width: 768px) {
    .floating-event-btn {
        bottom: 15px;
        right: 15px;
        padding: 12px 16px !important;
        min-height: 63px;
    }

    .event-date {
        font-size: 15px;
    }

    .event-time {
        font-size: 13px;
    }
}

/* Para pantallas muy pequeñas */
@media (max-width: 480px) {
    .floating-event-btn {
        bottom: 10px;
        right: 10px;
        padding: 9px 14px !important;
        min-height: 58px;
    }

    .event-date {
        font-size: 14px;
    }

    .event-time {
        font-size: 12px;
    }
}
</style>
