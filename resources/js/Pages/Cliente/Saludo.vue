<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed, onUnmounted, watch, nextTick } from 'vue';
import Preloader from '@/Components/Preloader.vue';

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
const showContent = ref(false);

// Video related refs
const currentVideo = ref<HTMLVideoElement | null>(null);
const videoLoaded = ref(false);
const selectedVideoPath = ref('');

// Cache maps para precarga de videos

// Construir nombre completo filtrando valores null/vacíos y capitalizando iniciales
const nombreCompleto = computed(() => {
    const partes = [
        props.cliente.nombre,
        props.cliente.apellido_paterno,
        props.cliente.apellido_materno
    ].filter(parte => parte && parte !== null && parte !== 'null' && parte.trim() !== '');

    // Capitalizar la primera letra de cada parte
    const partesCapitalizadas = partes.map(parte => {
        return parte.charAt(0).toUpperCase() + parte.slice(1).toLowerCase();
    });

    return partesCapitalizadas.join(' ');
});

// Índice del mensaje seleccionado aleatoriamente
const messageIndex = ref(0);

// Mensaje personalizado con nombre completo - selección aleatoria
const welcomeMessage = computed(() => {
    // Array de mensajes de saludo
    const welcomeMessages = [
        `¡Hola <span class="nombre-bold">${nombreCompleto.value}</span>!
Bienvenido a Innovation Day 2025.
El futuro comienza hoy.`,

        `¡Bienvenido <span class="nombre-bold">${nombreCompleto.value}</span>!
Gracias por ser parte del cambio.`,

        `¡Hola <span class="nombre-bold">${nombreCompleto.value}</span>!
Despierta tu mente, rompe los límites.
Innovation Day 2025 te espera.`,

        `¡Hola, <span class="nombre-bold">${nombreCompleto.value}</span>!
Tu visión es parte de esta revolución.
¡Bienvenido a Innovation Day 2025!`,

        `¡<span class="nombre-bold">${nombreCompleto.value}</span>, llegaste justo a tiempo!
Innovation Day 2025 está por despegar.
¿Listo para transformar el mañana?`,

        `¡<span class="nombre-bold">${nombreCompleto.value}</span>, Bienvenido al epicentro de la innovación!
Hoy las ideas se convierten en acción.`
    ];

    return welcomeMessages[messageIndex.value];
});

const handleLoaded = () => {
    isLoading.value = false;
};

// Configuración SEO personalizada
const seoConfig = computed(() => ({
    title: `¡Hola ${nombreCompleto.value}! - Innovation Day 2025`,
    description: `Saludo personalizado para ${nombreCompleto.value} en Innovation Day 2025`,
    ogImage: `${baseUrl.value}/images/banner.webp`,
    favicon: `${baseUrl.value}/images/favicon.png`
}));

// Función para seleccionar video aleatorio (como los mensajes)
const selectRandomVideo = (): string => {
    const videoNumber = Math.floor(Math.random() * 3) + 1; // Números del 1 al 3
    const videoPath = `/video/video_fondo_${videoNumber}.mp4`;
    console.log(`🎲 Video seleccionado aleatoriamente: ${videoPath}`);
    return videoPath;
};

// Función para precargar videos (simplificada)
const preloadVideos = (): void => {
    console.log('📹 Precargando videos...');

    // Precargar solo los videos que podrían ser necesarios
    for (let i = 1; i <= 3; i++) {
        const videoPath = `/video/video_fondo_${i}.mp4`;

        // Crear elemento link para precargar
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'video';
        link.href = videoPath;
        link.type = 'video/mp4';
        document.head.appendChild(link);
    }

    console.log('📹 Enlaces de precarga de videos creados');
};

// Función para reproducir video de fondo
const playBackgroundVideo = async (): Promise<void> => {
    try {
        // Obtener el elemento video del DOM
        const videoElement = document.querySelector('#background-video') as HTMLVideoElement;

        if (!videoElement) {
            console.error('❌ Elemento video no encontrado');
            return;
        }

        console.log(`🎬 Configurando video: ${videoElement.src}`);

        // Esperar a que se cargue el video si aún no está cargado
        if (videoElement.readyState < 3) { // HAVE_FUTURE_DATA
            await new Promise<void>((resolve, reject) => {
                const onLoadedData = () => {
                    console.log('✅ Video cargado correctamente');
                    videoElement.removeEventListener('loadeddata', onLoadedData);
                    videoElement.removeEventListener('error', onError);
                    resolve();
                };

                const onError = (error: Event) => {
                    console.error('❌ Error al cargar video:', error);
                    videoElement.removeEventListener('loadeddata', onLoadedData);
                    videoElement.removeEventListener('error', onError);
                    reject(error);
                };

                videoElement.addEventListener('loadeddata', onLoadedData);
                videoElement.addEventListener('error', onError);
            });
        }

        // Intentar reproducir el video
        try {
            await videoElement.play();
            currentVideo.value = videoElement;
            videoLoaded.value = true;
            console.log('✅ Video de fondo reproduciéndose');
        } catch (playError) {
            console.warn('⚠️ Video no pudo reproducirse automáticamente, esperando interacción del usuario');
            // El video se reproducirá cuando el usuario interactúe
            videoLoaded.value = true;
        }
    } catch (error) {
        console.error('❌ Error al reproducir video de fondo:', error);
        // Continuar sin video si hay error
        videoLoaded.value = true;
    }
};

// Funciones de audio eliminadas - solo se usa video y texto

// Efecto typewriter modificado para HTML
const startTypewriter = () => {
    let index = 0;
    const speed = 50;
    const message = welcomeMessage.value;

    const typeNextChar = () => {
        if (index < message.length) {
            // Si el caracter actual es '<', encontrar el final del tag
            if (message.charAt(index) === '<') {
                const endTag = message.indexOf('>', index);
                if (endTag !== -1) {
                    // Agregar todo el tag de una vez
                    typewriterText.value += message.substring(index, endTag + 1);
                    index = endTag + 1;
                } else {
                    // Si no hay cierre de tag, agregar el caracter normalmente
                    typewriterText.value += message.charAt(index);
                    index++;
                }
            } else {
                // Agregar caracter normal
                typewriterText.value += message.charAt(index);
                index++;
            }
            setTimeout(typeNextChar, speed);
        }
    };

    typeNextChar();
};

// Watcher para selectedVideoPath
watch(selectedVideoPath, async (newPath) => {
    if (newPath) {
        console.log(`📺 selectedVideoPath cambió a: ${newPath}`);

        // Esperar al siguiente tick para asegurar que el DOM esté actualizado
        await nextTick();

        const videoElement = document.querySelector('#background-video') as HTMLVideoElement;
        if (videoElement) {
            console.log(`🎬 Video element src: ${videoElement.src}`);
            console.log(`🎬 Video element readyState: ${videoElement.readyState}`);

            // Verificar si el src está configurado correctamente
            if (!videoElement.src || videoElement.src === window.location.href) {
                console.warn('⚠️ Video src no está configurado correctamente, configurando manualmente...');
                videoElement.src = newPath;
                videoElement.load();
            }
        }
    }
});

// Funciones de auto-click eliminadas junto con audio

onMounted(async () => {
    console.log('🎬 Componente montado, iniciando secuencia...');

    // Seleccionar mensaje aleatoriamente
    messageIndex.value = Math.floor(Math.random() * 6);
    console.log(`💬 Mensaje seleccionado aleatoriamente: ${messageIndex.value + 1}`);

    // Seleccionar video aleatoriamente para establecer el src
    selectedVideoPath.value = selectRandomVideo();
    console.log(`🎬 Video seleccionado: ${selectedVideoPath.value}`);

    // Precargar recursos
    preloadVideos();

    // Solo se precargan videos - audio eliminado

    // Dar tiempo adicional para que el video se configure
    setTimeout(async () => {
        console.log('🎬 Iniciando reproducción de video...');
        await playBackgroundVideo();

        // Ocultar preloader y mostrar contenido
        isLoading.value = false;
        showContent.value = true;

        // Iniciar typewriter
        setTimeout(() => {
            startTypewriter();
        }, 300);

        // Ya no se configura audio automático

        // Debug del video después de un delay
        setTimeout(() => {
            const videoElement = document.querySelector('#background-video') as HTMLVideoElement;
            if (videoElement) {
                console.log('🔍 DEBUG Video Status:');
                console.log(`  - src: ${videoElement.src}`);
                console.log(`  - readyState: ${videoElement.readyState}`);
                console.log(`  - paused: ${videoElement.paused}`);
                console.log(`  - currentTime: ${videoElement.currentTime}`);
                console.log(`  - duration: ${videoElement.duration}`);
                console.log(`  - error: ${videoElement.error}`);
                                console.log(`  - networkState: ${videoElement.networkState}`);
                console.log(`  - visibility: ${getComputedStyle(videoElement).visibility}`);
                console.log(`  - display: ${getComputedStyle(videoElement).display}`);
                console.log(`  - opacity: ${getComputedStyle(videoElement).opacity}`);
                console.log(`  - z-index: ${getComputedStyle(videoElement).zIndex}`);

                if (videoElement.paused && videoElement.readyState >= 2) {
                    console.log('⚠️ Video está pausado pero listo, intentando reproducir...');
                    videoElement.play().catch(error => {
                        console.error('❌ Error al reproducir video en debug:', error);
                    });
                }
            }
        }, 1000);
    }, 2000);
});

onUnmounted(() => {
    console.log('🧹 Limpiando recursos...');

    if (currentVideo.value) {
        currentVideo.value.pause();
        currentVideo.value = null;
    }

    // Cache de audio eliminado
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

            <!-- Contenedor principal que incluye video y contenido -->
            <div class="welcome-container">
                <!-- Video de fondo después del header -->
                <div class="video-background">
                    <video
                        id="background-video"
                        :src="selectedVideoPath"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="auto"
                        class="background-video"
                        @loadeddata="videoLoaded = true"
                        @error="console.error('Error loading video:', $event)"
                    />
                </div>

                <!-- Botón oculto eliminado junto con audio -->

                <!-- Contenido principal sobre el video -->
                <div class="main-content">
                    <transition name="fade-in">
                        <div v-if="showContent" class="content-overlay">
                            <!-- Card del mensaje posicionado específicamente -->
                            <div class="message-card">
                                <div class="typewriter-container">
                                    <p class="typewriter-text" v-html="typewriterText + '<span class=&quot;cursor&quot;>|</span>'"></p>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </v-main>
    </v-app>
</template>

<style scoped>
/* Estilos del botón de audio eliminados */

/* Asegurar que todos los contenedores sean transparentes */
:deep(body) {
    background: transparent !important;
}

:deep(html) {
    background: transparent !important;
}

:deep(.v-application) {
    background: transparent !important;
}

:deep(.v-main) {
    background: transparent !important;
}

:deep(.v-application__wrap) {
    background: transparent !important;
}

.top-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 120px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 4rem;
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
    height: 80px;
    width: auto;
    transition: transform 0.3s ease;
}

.logo-image:hover {
    transform: scale(1.05);
}

.innovation-image {
    height: 100px;
    width: auto;
    transition: transform 0.3s ease;
}

.innovation-image:hover {
    transform: scale(1.05);
}

.welcome-container {
    margin-top: 120px; /* Espacio para el header fijo */
    min-height: calc(100vh - 120px);
    position: relative;
    background: transparent;
    overflow: hidden;
}

.video-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    overflow: hidden;
}

.background-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    opacity: 1;
}

.main-content {
    position: relative;
    z-index: 2;
    min-height: calc(100vh - 120px);
    background: transparent;
}

.content-overlay {
    position: relative;
    width: 100%;
    height: calc(100vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
}

/* Posicionamiento específico del card para totem */
.message-card {
    position: absolute;
    /*
    POSICIONAMIENTO PERSONALIZABLE:
    - top: 0% = arriba del todo, 100% = abajo del todo
    - left: 0% = izquierda del todo, 100% = derecha del todo
    - También puedes usar: right, bottom en lugar de left, top

    EJEMPLOS DE POSICIONAMIENTO:
    - Esquina superior izquierda: top: 5%, left: 5%
    - Esquina superior derecha: top: 5%, right: 5%
    - Esquina inferior izquierda: bottom: 5%, left: 5%
    - Esquina inferior derecha: bottom: 5%, right: 5%
    - Centro: top: 50%, left: 50%, transform: translate(-50%, -50%)
    */
    top: 20%; /* Distancia desde arriba */
    left: 5%; /* Distancia desde la izquierda */

    /* Tamaño del card */
    max-width: 900px;
    width: 45%; /* Ancho relativo al contenedor */

    /* Asegurar que esté encima del video */
    z-index: 10;

    /* Para debugging - descomenta para ver el borde del card */
    /* border: 2px solid red; */
    /* background: rgba(255, 0, 0, 0.1); */
}

.typewriter-container {

    padding: 4rem;
    border-radius: 25px;
}

.typewriter-text {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 4.5rem;
    line-height: 1.4;
    color: #1a1a1a;
    margin: 0;
    white-space: pre-line;
    word-wrap: break-word;
    text-align: center;
    font-weight: 500;
}

/* Estilo para nombres en negritas */
.nombre-bold {
    font-weight: 700;
    font-size: 1.1em;
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

/* Transiciones */
.fade-in-enter-active {
    transition: all 2s ease-out;
}

.fade-in-enter-from {
    opacity: 0;
    transform: translateY(50px) scale(0.9);
}

.fade-in-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* Responsive para totem - Pantallas grandes */
@media (min-width: 1400px) {
    .top-header {
        height: 140px;
        padding: 0 5rem;
    }

    .logo-image {
        height: 100px;
    }

    .innovation-image {
        height: 120px;
    }

    .welcome-container {
        margin-top: 140px;
        min-height: calc(100vh - 140px);
    }

    .main-content {
        min-height: calc(100vh - 140px);
    }

    .content-overlay {
        height: calc(100vh - 140px);
    }

    .message-card {
        /* Ajustar posición para pantallas grandes */
        top: 1%; /* Ajustar según necesidad */
        left: 5%; /* Ajustar según necesidad */
        max-width: 1200px;
        width: 45%; /* Ajustar según necesidad */
    }

    .typewriter-container {
        padding: 5rem;
        border-radius: 30px;
    }

    .typewriter-text {
        font-size: 3.2rem;
        line-height: 1.5;
    }
}

/* Pantallas extra grandes (40+ pulgadas) */
@media (min-width: 1920px) {
    .top-header {
        height: 160px;
        padding: 0 6rem;
    }

    .logo-image {
        height: 120px;
    }

    .innovation-image {
        height: 140px;
    }

    .welcome-container {
        margin-top: 160px;
        min-height: calc(100vh - 160px);
    }

    .main-content {
        min-height: calc(100vh - 160px);
    }

    .content-overlay {
        height: calc(100vh - 160px);
    }

    .message-card {
        /* Ajustar posición para pantallas extra grandes */
        top: 1%; /* Ajustar según necesidad */
        left: 5%; /* Ajustar según necesidad */
        max-width: 1400px;
        width: 45%; /* Ajustar según necesidad */
    }

    .typewriter-container {
        padding: 6rem;
        border-radius: 35px;
    }

    .typewriter-text {
        font-size: 3.2rem;
        line-height: 1.4;
    }
}

/* Responsive - Tablets */
@media (max-width: 1399px) and (min-width: 768px) {
    .top-header {
        height: 100px;
        padding: 0 3rem;
    }

    .logo-image {
        height: 60px;
    }

    .innovation-image {
        height: 80px;
    }

    .welcome-container {
        margin-top: 100px;
        min-height: calc(100vh - 100px);
    }

    .main-content {
        min-height: calc(100vh - 100px);
    }

    .content-overlay {
        height: calc(100vh - 100px);
    }

    .message-card {
        /* Ajustar posición para tablets */
        top: 1%; /* Ajustar según necesidad */
        left: 1%; /* Ajustar según necesidad */
        max-width: 800px;
        width: 74%; /* Ajustar según necesidad */
    }

    .typewriter-container {
        padding: 2.9rem;
    }

    .typewriter-text {
        font-size: 2.75rem;
    }
}

/* Responsive - Móviles */
@media (max-width: 767px) {
    .top-header {
        height: 80px;
        padding: 0 2rem;
    }

    .logo-image {
        height: 45px;
    }

    .innovation-image {
        height: 60px;
    }

    .welcome-container {
        margin-top: 80px;
        min-height: calc(100vh - 80px);
    }

    .main-content {
        min-height: calc(100vh - 80px);
    }

    .content-overlay {
        height: calc(100vh - 80px);
    }

    .message-card {
        /* Ajustar posición para móviles */
        top: 1%; /* Ajustar según necesidad */
        left: 5%; /* Ajustar según necesidad */
        max-width: 95%;
        width: 90%; /* Ajustar según necesidad */
    }

    .typewriter-container {
        padding: 2rem;
        border-radius: 20px;
    }

    .typewriter-text {
        font-size: 1.4rem;
    }
}
</style>
