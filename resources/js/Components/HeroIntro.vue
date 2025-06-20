<script setup lang="ts">
import bannerImage from '../../images/banner.webp';

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
                    <h1 class="hero-title mb-6">
                        Anticípate al futuro.<br>
                        Lidera el cambio
                    </h1>
                    <p class="hero-description mb-4">
                        Pentafon, en colaboración con Microsoft, te invita a un evento exclusivo diseñado
                         para líderes que buscan transformar la forma en que se enfrentan
                          los desafíos del mañana.

                    </p>
                    <p class="event-info mb-8">
                        📍 Evento presencial | <em>Cupo limitado</em>
                    </p>
                    <v-btn
                        color="#eb1c2d"
                        size="x-large"
                        rounded="pill"
                        @click="scrollToForm"
                        class="px-8 text-white"
                        elevation="2"
                    >
                        Regístrate ahora
                    </v-btn>
                </v-col>

                <!-- Imagen del banner -->
                <v-col cols="12" md="6" lg="6" class="pa-0 hero-image-col">
                    <div class="hero-image-container">
                        <img
                            :src="bannerImage"
                            alt="Innovation Day Banner"
                            class="hero-image"
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

.hero-description {
    font-size: 18px;
    line-height: 1.6;
    color: #333;
}

.event-info {
    font-size: 16px;
    font-weight: 500;
    color: #333;
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

/* Estilos para móvil */
@media (max-width: 959px) {
    .hero-section {
        overflow: visible;
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

/* Estilos para escritorio */
@media (min-width: 960px) {
    .hero-image-col {
        position: absolute;
        right: 0;
        bottom: 0;
        height: calc(100vh - 64px);
        width: 50%;
        max-width: none;
    }

    .hero-image-container {
        height: 100%;
    }
}
</style>
