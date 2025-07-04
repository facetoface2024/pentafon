<script setup lang="ts">
import bannerImage from '../../images/banner.webp';
import frase2Image from '../../images/frase-2.png';
import backgroundWebImage from '../../images/background_web.jpg';

const scrollToForm = () => {
    const element = document.getElementById('registro');
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>

<template>
    <section class="hero-section">
        <v-container fluid class="pa-0">
            <v-row class="align-center ma-0" style="min-height: calc(100vh - 64px)">
                <v-col cols="12" md="6" lg="6" class="pa-8 pa-md-12">
                    <h1 class="hero-title-image mb-6">
                        <img :src="frase2Image" alt="Innovation Day 2025 - Anticípate al futuro, lidera el cambio - Pentafon y Microsoft" class="title-image" loading="eager" fetchpriority="high" width="800" height="200" />
                    </h1>
                    <p class="hero-description mb-4">
                        <strong>Pentafon</strong> y <strong class="text-highlight">Microsoft</strong> unen fuerzas para abrir un espacio donde la innovación deja de ser tendencia... y se convierte en estrategia.
                    </p>
                    <p class="hero-description mb-4">
                        Explora cómo las megatendencias tecnológicas están redefiniendo <strong>los negocios, la experiencia del cliente y las decisiones estratégicas</strong> más audaces.
                       <br> Un evento diseñado para líderes que no solo quieren adaptarse al futuro, sino construirlo.
                    </p>
                    <p class="event-info mb-8">
                        📍 <strong>Evento presencial</strong> | <em class="text-highlight">Cupo limitado</em>
                    </p>
                    <div class="cta-button-container">
                        <v-btn
                            color="#eb1c2d"
                            size="x-large"
                            rounded="pill"
                            @click="scrollToForm"
                            class="px-8 text-white cta-button"
                            elevation="2"
                        >
                            <strong>Prerregístrate ahora</strong>
                        </v-btn>
                    </div>
                </v-col>

                <!-- Imagen del banner - Solo visible en móvil -->
                <v-col cols="12" md="6" lg="6" class="pa-0 hero-image-col mobile-only">
                    <div class="hero-image-container">
                        <img
                            :src="bannerImage"
                            alt="Innovation Day Banner"
                            class="hero-image"
                            loading="lazy"
                        />
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<style scoped>
.hero-section {
    position: relative;
    background-color: #fdfdfb;
    overflow-x: hidden;
}

/* Fondo para web/desktop */
@media (min-width: 960px) {
    .hero-section {
        background-image: url('../../images/background_web.jpg');
        background-size: max(100vw, calc(100vh * 1.7778)) max(calc(100vw * 0.5625), 100vh);
        background-repeat: no-repeat;
        background-position: center center;
        background-attachment: scroll;
        min-height: 100vh;
    }
}

.innovation-text {
    font-size: 24px;
    font-weight: 300;
    color: #666;
    letter-spacing: 2px;
    text-transform: lowercase;
}

.hero-title {
    font-size: clamp(36px, 5vw, 56px);
    font-weight: 700;
    line-height: 1.2;
    color: #000;
}

.hero-title-image {
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.title-image {
    max-width: 100%;
    height: auto;
    width: auto;
    max-height: 120px;
}

.text-highlight {
    color: #eb1c2d;
}

.text-bold {
    font-weight: 700;
    color: #000;
}

.hero-description {
    font-size: 18px;
    line-height: 1.6;
    color: #333;
}

.hero-description strong {
    color: #000;
}

.event-info {
    font-size: 16px;
    font-weight: 500;
    color: #333;
}

.cta-button-container {
    display: flex;
    justify-content: flex-start;
}

/* Centrar botón solo en web/desktop */
@media (min-width: 960px) {
    .cta-button-container {
        justify-content: center;
    }
}

.hero-image-col {
    display: flex;
    align-items: flex-end;
}

.hero-image-container {
    width: 100%;
    height: 100%;
    position: relative;
    display: flex;
    align-items: flex-end;
}

.hero-image {
    width: 100%;
    height: auto;
    max-height: calc(100vh - 64px);
    object-fit: contain;
    object-position: bottom right;
}

/* Ocultar imagen en web/desktop */
@media (min-width: 960px) {
    .mobile-only {
        display: none !important;
    }
}

/* Estilos para móvil - mantener exactamente como estaba */
@media (max-width: 959px) {
    .hero-section {
        overflow: visible;
        background-image: none !important;
    }

    .hero-image-col {
        position: static;
        width: 100vw;
        max-width: none;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
    }

    .hero-image-container {
        height: auto;
        margin-top: 2rem;
        width: 100%;
    }

    .hero-image {
        height: 80vh;
        min-height: 400px;
        max-height: 600px;
        width: 100%;
        object-fit: fill;
        object-position: center bottom;
    }
}

.cta-button {
    transform: translateY(0);
    transition: all 0.3s ease;
}

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(235, 28, 45, 0.3) !important;
}
</style>
