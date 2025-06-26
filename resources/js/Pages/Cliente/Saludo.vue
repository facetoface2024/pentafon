<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import Preloader from '@/Components/Preloader.vue';
import bannerImage from '../../../images/banner.webp';

const page = usePage();
const baseUrl = computed(() => {
    const ziggy = (page.props as any).ziggy;
    return ziggy?.url || window.location.origin;
});

// Props
interface Cliente {
    nombre_completo: string;
    nombre: string;
    apellido_paterno: string;
    apellido_materno: string;
    correo: string;
}

interface Props {
    cliente: Cliente;
}

const props = defineProps<Props>();

const isLoading = ref(true);
const typewriterText = ref('');
const showImage = ref(false);

// Mensaje personalizado con nombre completo
const welcomeMessage = `¡Hola ${props.cliente.nombre} ${props.cliente.apellido_paterno} ${props.cliente.apellido_materno}!

Bienvenido a Innovation Day 2025 🎉

Gracias por estar aquí. El futuro te espera con todas las innovaciones que descubrirás hoy.

¡Prepárate para una experiencia única! ✨`;

const handleLoaded = () => {
    isLoading.value = false;
};

// Configuración SEO personalizada
const seoConfig = computed(() => ({
    title: `¡Hola ${props.cliente.nombre} ${props.cliente.apellido_paterno} ${props.cliente.apellido_materno}! - Innovation Day 2025`,
    description: `Saludo personalizado para ${props.cliente.nombre_completo} en Innovation Day 2025`,
    ogImage: `${baseUrl.value}/images/banner.webp`,
    favicon: `${baseUrl.value}/images/favicon.png`
}));

// Efecto typewriter
const startTypewriter = () => {
    let index = 0;
    const speed = 50; // Velocidad de escritura en ms

    const typeNextChar = () => {
        if (index < welcomeMessage.length) {
            typewriterText.value += welcomeMessage.charAt(index);
            index++;
            setTimeout(typeNextChar, speed);
        }
    };

    typeNextChar();
};

onMounted(() => {
    // Ocultar el preloader después de 2 segundos y comenzar todo
    setTimeout(() => {
        isLoading.value = false;
        // Mostrar la imagen inmediatamente al cargar
        showImage.value = true;
        // Esperar un poco más y comenzar el efecto typewriter
        setTimeout(() => {
            startTypewriter();
        }, 300);
    }, 2000);
});
</script>

<template>
    <Head>
        <title>{{ seoConfig.title }}</title>
        <meta name="description" :content="seoConfig.description" />
        <meta name="robots" content="noindex, nofollow" />
        <link rel="icon" type="image/png" :href="seoConfig.favicon" />

        <!-- Open Graph -->
        <meta property="og:title" :content="seoConfig.title" />
        <meta property="og:description" :content="seoConfig.description" />
        <meta property="og:image" :content="seoConfig.ogImage" />
        <meta property="og:type" content="website" />
    </Head>

    <v-app>
        <Preloader :is-loading="isLoading" @loaded="handleLoaded" />

        <v-main>
            <!-- Contenedor principal -->
            <div class="welcome-container">
                <!-- Header con logos -->
                <header class="top-header">
                    <div class="logo-left">
                        <img
                            :src="`${baseUrl}/images/logo.svg`"
                            alt="Pentafon Logo"
                            class="logo-image"
                        />
                    </div>
                    <div class="logo-right">
                        <img
                            :src="`${baseUrl}/images/innovation.svg`"
                            alt="Innovation Logo"
                            class="innovation-image"
                        />
                    </div>
                </header>

                                <!-- Contenido principal responsive -->
                <v-container fluid class="main-content pa-0">
                    <v-row class="fill-height align-center ma-0" no-gutters>
                                                <!-- Columna del mensaje - Arriba siempre -->
                        <v-col
                            cols="12"
                            class="message-column pa-0"
                            order="1"
                        >
                            <div class="message-container">
                                <div class="typewriter-container">
                                    <p class="typewriter-text">{{ typewriterText }}<span class="cursor">|</span></p>
                                </div>
                            </div>
                        </v-col>

                        <!-- Columna de la imagen - Abajo siempre -->
                        <v-col
                            cols="12"
                            class="image-column pa-0"
                            order="2"
                        >
                            <div class="image-container">
                                <transition name="fade-in">
                                    <img
                                        v-if="showImage"
                                        :src="bannerImage"
                                        alt="Innovation Day Banner"
                                        class="banner-image"
                                        loading="lazy"
                                    />
                                </transition>
                            </div>
                        </v-col>
                    </v-row>
                </v-container>
            </div>
        </v-main>
    </v-app>
</template>

<style scoped>
.welcome-container {
    min-height: 100vh;
    background-image: url('/images/fondo1.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    position: relative;
}

/* Overlay semitransparente */
.welcome-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.1);
    z-index: 1;
    pointer-events: none;
}

.top-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    z-index: 1000;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.logo-left, .logo-right {
    display: flex;
    align-items: center;
}

.logo-image {
    height: 60px;
    width: auto;
    transition: transform 0.3s ease;
}

.logo-image:hover {
    transform: scale(1.05);
}

.innovation-image {
    height: 90px;
    width: auto;
    transition: transform 0.3s ease;
}

.innovation-image:hover {
    transform: scale(1.05);
}

.main-content {
    padding-top: 120px;
    min-height: calc(100vh - 120px);
    position: relative;
    z-index: 2;
}

.message-column {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.message-container {
    max-width: 90%;
    width: 100%;
}

.typewriter-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.typewriter-text {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 1.4rem;
    line-height: 1.6;
    color: #1a1a1a;
    margin: 0;
    white-space: pre-line;
    word-wrap: break-word;
}

.cursor {
    animation: blink 1s infinite;
    color: #eb1c2d;
    font-weight: bold;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0; }
}

.image-column {
    display: flex;
    align-items: stretch;
    justify-content: stretch;
    padding: 0;
}

.image-container {
    width: 100vw;
    height: 100%;
    display: flex;
    align-items: stretch;
    justify-content: stretch;
    position: relative;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
}

.banner-image {
    width: 100%;
    height: 100%;
    min-height: calc(100vh - 140px);
    object-fit: fill;
    object-position: center;
}

/* Transiciones */
.fade-in-enter-active {
    transition: all 1.5s ease-out;
}

.fade-in-enter-from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
}

.fade-in-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* Responsive - Mobile First (Vertical) */
@media (max-width: 959px) {
    .welcome-container {
        background-attachment: scroll;
    }

    .top-header {
        height: 80px;
        padding: 0 1.5rem;
    }

    .logo-image {
        height: 45px;
    }

    .innovation-image {
        height: 40px;
    }

    .main-content {
        padding-top: 100px;
        min-height: calc(100vh - 100px);
    }

    .message-column {
        padding: 1.5rem;
        min-height: 50vh;
    }

    .typewriter-container {
        padding: 2rem;
        margin-bottom: 1rem;
    }

    .typewriter-text {
        font-size: 1.2rem;
        text-align: center;
    }

    .image-column {
        padding: 0;
        min-height: 50vh;
        margin: 0;
    }

    .image-container {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
    }

    .banner-image {
        min-height: 50vh;
        width: 100%;
        height: 100%;
        object-fit: fill;
    }
}

/* Móviles pequeños */
@media (max-width: 480px) {
    .top-header {
        height: 70px;
        padding: 0 1rem;
    }

    .logo-image {
        height: 35px;
    }

    .innovation-image {
        height: 30px;
    }

    .main-content {
        padding-top: 90px;
    }

    .message-column {
        padding: 1rem;
    }

    .image-column {
        padding: 0;
        margin: 0;
    }

    .image-container {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
    }

    .typewriter-container {
        padding: 1.5rem;
        border-radius: 15px;
    }

    .typewriter-text {
        font-size: 1.1rem;
    }

    .banner-image {
        min-height: 45vh;
        height: 100%;
        object-fit: fill;
    }
}

/* Desktop - Layout vertical igual que móvil pero con márgenes mínimos */
@media (min-width: 960px) {
    .main-content {
        padding-top: 120px;
    }

    .message-column {
        padding: 1rem 2rem; /* Márgenes mínimos en los lados */
        min-height: 40vh;
    }

    .image-column {
        padding: 0;
        margin: 0;
        min-height: 60vh;
    }

    .image-container {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
    }

    .banner-image {
        width: 100%;
        height: 100%;
        min-height: 60vh;
        object-fit: fill;
        object-position: center;
    }

    .typewriter-text {
        font-size: 1.5rem;
        text-align: center; /* Centrado igual que móvil */
    }

    .typewriter-container {
        padding: 2.5rem;
        max-width: 800px;
        margin: 0 auto;
    }
}

/* Pantallas muy grandes */
@media (min-width: 1400px) {
    .typewriter-container {
        padding: 4rem;
    }

    .typewriter-text {
        font-size: 1.6rem;
    }
}
</style>
