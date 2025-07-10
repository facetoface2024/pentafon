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
const showContent = ref(false);

// Variables para animaciones secuenciales
const showTopText = ref(false);
const showName = ref(false);
const showBottomText = ref(false);

// Video related refs
const currentVideo = ref<HTMLVideoElement | null>(null);
const videoLoaded = ref(false);
const selectedVideoPath = ref('');

// Construir nombre completo usando solo nombre y apellido paterno con iniciales en mayúsculas
const nombreCompleto = computed(() => {
    const partes = [
        props.cliente.nombre,
        props.cliente.apellido_paterno
    ].filter(parte => parte && parte !== null && parte !== 'null' && parte.trim() !== '');

    // Capitalizar solo la primera letra de cada parte, manteniendo el resto como está en BD
    const partesCapitalizadas = partes.map(parte => {
        return parte.charAt(0).toUpperCase() + parte.slice(1);
    });

    return partesCapitalizadas.join(' ');
});

// Índice del mensaje seleccionado aleatoriamente
const messageIndex = ref(0);

// Estructura de mensajes separados por partes
const messageStructure = computed(() => {
    const messages = [
        {
            topText: '¡Hola!',
            bottomText: 'Bienvenido.\nEl futuro comienza hoy'
        },
        {
            topText: '¡Bienvenido!',
            bottomText: 'Gracias por ser parte del cambio'
        },
        {
            topText: '¡Hola!',
            bottomText: 'Despierta tu mente, \n rompe los límites.\nBienvenido, el futuro te espera'
        },
        {
            topText: '¡Hola!',
            bottomText: 'Tu visión es parte de \n esta revolución.\n¡Bienvenido!'
        },
        {
            topText: '',
            bottomText: '¡Llegaste justo a tiempo!\nInnovation Day 2025 \n está  por despegar. \n ¿Listo para transformar el mañana?'
        },
        {
            topText: '',
            bottomText: '¡Bienvenido al epicentro de la innovación!\nHoy las ideas se convierten en acción.'
        }
    ];

    return messages[messageIndex.value];
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

// Función para seleccionar video de forma secuencial
const selectSequentialVideo = (): string => {
    // Obtener el último video usado del localStorage
    const lastVideoNumber = parseInt(localStorage.getItem('lastVideoNumber') || '0');

    // Calcular el siguiente video en secuencia (1, 2, 3, 1, 2, 3...)
    const nextVideoNumber = (lastVideoNumber % 3) + 1;

    // Guardar el nuevo número en localStorage
    localStorage.setItem('lastVideoNumber', nextVideoNumber.toString());

    const videoPath = `/video/fondo_video_${nextVideoNumber}.mp4`;
    return videoPath;
};

// Función para verificar si los videos están en cache
const checkVideoCache = (): boolean => {
    const cacheKey = 'videos_preloaded';
    const cacheTimestamp = localStorage.getItem(cacheKey);

    if (!cacheTimestamp) return false;

    // Verificar si el cache no es muy antiguo (24 horas)
    const now = Date.now();
    const cacheAge = now - parseInt(cacheTimestamp);
    const maxCacheAge = 24 * 60 * 60 * 1000; // 24 horas

    return cacheAge < maxCacheAge;
};

// Función para limpiar cache de videos si es necesario
const clearVideoCache = async (): Promise<void> => {
    // Limpiar localStorage
    localStorage.removeItem('videos_preloaded');
    localStorage.removeItem('lastVideoNumber');

    // Limpiar cache del navegador si está disponible
    if ('caches' in window) {
        try {
            const cacheNames = await caches.keys();
            const videoCache = cacheNames.find(name => name.includes('video-cache'));
            if (videoCache) {
                await caches.delete(videoCache);

            }
        } catch (error) {
            console.warn('⚠️ No se pudo limpiar cache del navegador:', error);
        }
    }

};

// Función para obtener información del cache
const getCacheInfo = async (): Promise<void> => {
    const localStorageInfo = {
        videos_preloaded: localStorage.getItem('videos_preloaded'),
        lastVideoNumber: localStorage.getItem('lastVideoNumber')
    };



    if ('caches' in window) {
        try {
            const cacheNames = await caches.keys();


            for (const cacheName of cacheNames) {
                if (cacheName.includes('video')) {
                    const cache = await caches.open(cacheName);
                    const keys = await cache.keys();

                }
            }
        } catch (error) {
            console.warn('⚠️ No se pudo obtener información del cache:', error);
        }
    }
};

// Función para optimizar cache con headers adicionales
const optimizeVideoCache = (videoElement: HTMLVideoElement): void => {
    // Configurar headers de cache para mejor rendimiento
    videoElement.setAttribute('crossorigin', 'anonymous');
    videoElement.setAttribute('preload', 'auto');

    // Configurar propiedades para mejor cache
    videoElement.setAttribute('webkit-playsinline', 'true');
    videoElement.setAttribute('playsinline', 'true');

    // Verificar si el navegador soporta cache API
    if ('caches' in window) {
        // Intentar agregar el video al cache del navegador
        caches.open('video-cache-v1').then(cache => {
            cache.add(videoElement.src).catch(error => {
                console.warn('⚠️ No se pudo agregar video al cache:', error);
            });
        }).catch(error => {
            console.warn('⚠️ No se pudo abrir cache:', error);
        });
    }


};

// Función mejorada para precargar videos con cache optimizado
const preloadVideos = (): Promise<void> => {


    // Verificar si los videos ya están en cache
    if (checkVideoCache()) {
        return Promise.resolve();
    }

    return new Promise((resolve) => {
        const videosToPreload = [1, 2, 3];
        let videosLoaded = 0;
        const totalVideos = videosToPreload.length;

        const checkAllLoaded = () => {
            videosLoaded++;
            if (videosLoaded === totalVideos) {

                // Marcar videos como precargados en localStorage
                localStorage.setItem('videos_preloaded', Date.now().toString());
                resolve();
            }
        };

        videosToPreload.forEach((videoNumber) => {
            const videoPath = `/video/fondo_video_${videoNumber}.mp4`;

            // Crear elemento video invisible para precarga real
            const video = document.createElement('video');
            video.preload = 'auto';
            video.muted = true;
            video.style.display = 'none';
            video.src = videoPath;

            // Configurar cache headers
            video.setAttribute('crossorigin', 'anonymous');

            video.addEventListener('loadeddata', () => {

                // Remover el elemento temporal del DOM
                document.body.removeChild(video);
                checkAllLoaded();
            });

            video.addEventListener('error', (error) => {
                console.warn(`⚠️ Error precargando video ${videoNumber}:`, error);
                // Remover el elemento temporal del DOM
                if (document.body.contains(video)) {
                    document.body.removeChild(video);
                }
                checkAllLoaded(); // Continuar aunque falle uno
            });

            // Agregar al DOM temporalmente para activar la precarga
            document.body.appendChild(video);

            // Crear también el link preload para doble seguridad
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'video';
            link.href = videoPath;
            link.type = 'video/mp4';
            document.head.appendChild(link);
        });
    });
};

// Función para configurar el loop personalizado del video
const setupVideoLoop = (videoElement: HTMLVideoElement): void => {
    const handleTimeUpdate = () => {
        // Cuando el video llegue al segundo 12, saltar al segundo 8
        if (videoElement.currentTime >= 12) {

            videoElement.currentTime = 8;
        }
    };

    // Agregar el listener para el tiempo del video
    videoElement.addEventListener('timeupdate', handleTimeUpdate);

    // Guardar la función para poder removerla después
    (videoElement as any).loopHandler = handleTimeUpdate;


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



        // Esperar a que se cargue el video si aún no está cargado
        if (videoElement.readyState < 3) { // HAVE_FUTURE_DATA
            await new Promise<void>((resolve, reject) => {
                const onLoadedData = () => {

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

        // Optimizar cache del video
        optimizeVideoCache(videoElement);

        // Configurar el loop personalizado antes de reproducir
        setupVideoLoop(videoElement);

        // Intentar reproducir el video
        try {
            await videoElement.play();
            currentVideo.value = videoElement;
            videoLoaded.value = true;

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

// Función para iniciar las animaciones secuenciales
const startSequentialAnimations = () => {

    // Mostrar texto superior primero (500ms + 20% = 600ms)
    setTimeout(() => {
        showTopText.value = true;
    }, 2000);

    // Mostrar nombre después (1200ms + 20% = 1440ms)
    setTimeout(() => {
        showName.value = true;
    }, 4000);

    // Mostrar texto inferior al final (1900ms + 20% = 2280ms)
    setTimeout(() => {
        showBottomText.value = true;
    }, 6000);
};

// Watcher para selectedVideoPath
watch(selectedVideoPath, async (newPath) => {
    if (newPath) {


        // Esperar al siguiente tick para asegurar que el DOM esté actualizado
        await nextTick();

        const videoElement = document.querySelector('#background-video') as HTMLVideoElement;
        if (videoElement) {

            // Verificar si el src está configurado correctamente
            if (!videoElement.src || videoElement.src === window.location.href) {
                console.warn('⚠️ Video src no está configurado correctamente, configurando manualmente...');
                videoElement.src = newPath;
                videoElement.load();
            }
        }
    }
});

onMounted(async () => {

    // Exponer funciones de cache globalmente para debug
    (window as any).clearVideoCache = clearVideoCache;
    (window as any).getCacheInfo = getCacheInfo;

    // Mostrar información del cache al inicio
    await getCacheInfo();

    // Seleccionar mensaje aleatoriamente
    messageIndex.value = Math.floor(Math.random() * 6);


    // Seleccionar video secuencialmente para establecer el src
    selectedVideoPath.value = selectSequentialVideo();


    try {
        // Precargar recursos y esperar a que terminen
        await preloadVideos();


        // Dar tiempo adicional para que el video se configure
        setTimeout(async () => {
            await playBackgroundVideo();

            // Ocultar preloader y mostrar contenido
            isLoading.value = false;
            showContent.value = true;

            // Iniciar animaciones secuenciales
            setTimeout(() => {
                startSequentialAnimations();
            }, 300);

            // Debug del video después de un delay
            setTimeout(() => {
                const videoElement = document.querySelector('#background-video') as HTMLVideoElement;
                if (videoElement) {
                    if (videoElement.paused && videoElement.readyState >= 2) {
                        videoElement.play().catch(error => {
                            console.error('❌ Error al reproducir video en debug:', error);
                        });
                    }
                }
            }, 1000);
        }, 1000); // Reducido de 2000 a 1000 ya que ya tenemos los videos precargados
    } catch (error) {
        console.error('❌ Error en el proceso de carga:', error);
        // Continuar con la carga aunque falle la precarga
        isLoading.value = false;
        showContent.value = true;
    }
});

onUnmounted(() => {

    if (currentVideo.value) {
        // Limpiar el event listener del loop personalizado
        const loopHandler = (currentVideo.value as any).loopHandler;
        if (loopHandler) {
            currentVideo.value.removeEventListener('timeupdate', loopHandler);
        }

        currentVideo.value.pause();
        currentVideo.value = null;
    }
});
</script>

<template>
    <Head>
        <title>{{ seoConfig.title }}</title>
        <meta name="description" :content="seoConfig.description" />
        <meta name="robots" content="noindex, nofollow" />
        <link rel="icon" type="image/png" :href="seoConfig.favicon" />

        <!-- Cargar fuente Prometo Trial -->
        <link href="https://fonts.cdnfonts.com/css/prometo-trial" rel="stylesheet">

        <!-- Open Graph -->
        <meta property="og:title" :content="seoConfig.title" />
        <meta property="og:description" :content="seoConfig.description" />
        <meta property="og:image" :content="seoConfig.ogImage" />
        <meta property="og:type" content="website" />
    </Head>

    <v-app>
        <Preloader :is-loading="isLoading" @loaded="handleLoaded" />

        <v-main>
            <!-- Contenedor principal que incluye video y contenido -->
            <div class="welcome-container">
                <!-- Video de fondo después del header -->
                <div class="video-background">
                    <video
                        id="background-video"
                        :src="selectedVideoPath"
                        autoplay
                        muted
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
                    <div v-if="showContent" class="content-overlay">
                        <!-- Card del mensaje posicionado específicamente -->
                        <div class="message-card">
                            <!-- Texto superior -->
                            <transition name="fade-in">
                                <div v-if="showTopText && messageStructure.topText" class="text-section">
                                    <p class="message-text">{{ messageStructure.topText }}</p>
                                </div>
                            </transition>

                            <!-- Nombre -->
                            <transition name="fade-in">
                                <div v-if="showName" class="text-section">
                                    <p class="message-text nombre-text">{{ nombreCompleto }}</p>
                                </div>
                            </transition>

                            <!-- Texto inferior -->
                            <transition name="fade-in">
                                <div v-if="showBottomText" class="text-section">
                                    <p class="message-text bottom-text">{{ messageStructure.bottomText }}</p>
                                </div>
                            </transition>
                        </div>
                    </div>
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

.welcome-container {
    min-height: 100vh;
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
    min-height: 100vh;
    background: transparent;
}

.content-overlay {
    position: relative;
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
}

/* Posicionamiento específico del card para totem */
.message-card {
    position: absolute;
    top: 20%; /* Distancia desde arriba */
    left: 5%; /* Distancia desde la izquierda */
    max-width: 1000px;
    width: 45%; /* Ancho relativo al contenedor */
    z-index: 10;
}

.text-section {
    border-radius: 25px;
}

.message-text {
    font-family: 'Prometo Trial', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 6rem;
    line-height: 1.4;
    color: #1a1a1a;
    margin: 0;
    white-space: pre-line;
    word-wrap: break-word;
    text-align: center;
    font-weight: 500;
}

/* Estilo específico para el nombre */
.nombre-text {
    color: #eb1c2d;
    font-weight: 900;
    font-size: 1.1em;
}

/* Estilo para texto inferior */
.bottom-text {
    margin-top: 1rem;
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
    .message-card {
        /* Ajustar posición para pantallas grandes */
        top: 1%; /* Ajustar según necesidad */
        left: 5%; /* Ajustar según necesidad */
        max-width: 1200px;
        width: 45%; /* Ajustar según necesidad */
    }

    .text-section {
        padding: 1rem 4rem;
        border-radius: 30px;
    }

    .message-text {
        font-size: 3.84rem;
        line-height: 1.5;
    }
}

/* Pantallas extra grandes (40+ pulgadas) */
@media (min-width: 1920px) {
    .message-card {
        /* Ajustar posición para pantallas extra grandes */
        top: 1%; /* Ajustar según necesidad */
        left: 5%; /* Ajustar según necesidad */
        max-width: 1400px;
        width: 45%; /* Ajustar según necesidad */
    }

    .text-section {
        padding: 2rem 6rem;
        border-radius: 35px;
    }

    .message-text {
        font-size: 3.84rem;
        line-height: 1.4;
    }
}

/* Responsive - Tablets */
@media (max-width: 1399px) and (min-width: 768px) {
    .message-card {
        /* Ajustar posición para tablets */
        top: 24%; /* Ajustar según necesidad */
        left: 7%; /* Ajustar según necesidad */
        max-width: 1000px;
        width: 100%; /* Ajustar según necesidad */
    }

   /*  .text-section {
        padding: 0.5rem 2rem;
    } */

    .message-text {
        font-size: 3.84rem;
    }
}

/* Responsive - Móviles */
@media (max-width: 767px) {
    .message-card {
        /* Ajustar posición para móviles */
        top: 1%; /* Ajustar según necesidad */
        left: 5%; /* Ajustar según necesidad */
        max-width: 100%;
        width: 90%; /* Ajustar según necesidad */
    }

    .text-section {
        padding: 1rem 2rem;
        border-radius: 20px;
    }

    .message-text {
        font-size: 1.68rem;
    }
}
</style>
