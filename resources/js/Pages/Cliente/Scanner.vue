<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const videoElement = ref<HTMLVideoElement | null>(null);
const canvasElement = ref<HTMLCanvasElement | null>(null);
const stream = ref<MediaStream | null>(null);
const scanning = ref(false);
const scannedCodes = ref<string[]>([]);
const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('success');

const mostrarSnackbar = (mensaje: string, color: string = 'success') => {
    snackbarMessage.value = mensaje;
    snackbarColor.value = color;
    snackbar.value = true;
};

const iniciarScanner = async () => {
    scanning.value = true;

    try {
        stream.value = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        });

        if (videoElement.value) {
            videoElement.value.srcObject = stream.value;
            videoElement.value.play();
        }

        // Iniciar detección QR
        iniciarDeteccionQr();
        mostrarSnackbar('📱 Escáner iniciado correctamente', 'success');
    } catch (error) {
        mostrarSnackbar('❌ Error al acceder a la cámara', 'error');
        console.error('Error:', error);
        scanning.value = false;
    }
};

const detenerScanner = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
        stream.value = null;
    }
    scanning.value = false;
    mostrarSnackbar('⏹️ Escáner detenido', 'info');
};

const iniciarDeteccionQr = () => {
    if (!videoElement.value || !canvasElement.value) return;

    const canvas = canvasElement.value;
    const context = canvas.getContext('2d');
    const video = videoElement.value;

    const detectar = () => {
        if (!scanning.value) return;

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context?.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Para demostración, simularemos que cada 10 segundos detecta un QR de ejemplo
            // En producción, aquí usarías jsQR u otra librería de detección
            // const imageData = context?.getImageData(0, 0, canvas.width, canvas.height);
            // const code = jsQR(imageData.data, imageData.width, imageData.height);
            // if (code) {
            //     procesarCodigoQr(code.data);
            // }
        }

        requestAnimationFrame(detectar);
    };

    detectar();

    // Simulación de detección para demo (remover en producción)
    if (process.env.NODE_ENV === 'development') {
        setTimeout(() => {
            if (scanning.value) {
                procesarCodigoQr(window.location.origin + '/saludo/ejemplo-token-demo');
            }
        }, 5000);
    }
};

const procesarCodigoQr = (codigo: string) => {
    if (!scannedCodes.value.includes(codigo)) {
        scannedCodes.value.push(codigo);
        mostrarSnackbar(`✅ QR escaneado: ${codigo}`, 'success');

        // Si es una URL, abrirla en nueva pestaña
        if (codigo.startsWith('http')) {
            window.open(codigo, '_blank');
        }
    }
};

const limpiarHistorial = () => {
    scannedCodes.value = [];
    mostrarSnackbar('🗑️ Historial limpiado', 'info');
};

const abrirUrl = (url: string) => {
    window.open(url, '_blank');
};

onMounted(() => {
    // Auto-iniciar scanner
    iniciarScanner();
});

onUnmounted(() => {
    detenerScanner();
});
</script>

<template>
    <Head title="Escáner QR - Sistema QR" />

    <v-app>
        <v-app-bar color="primary" dark>
            <v-app-bar-title>
                <v-icon class="tw-mr-2">mdi-qrcode-scan</v-icon>
                Escáner QR
            </v-app-bar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="detenerScanner" v-if="scanning">
                <v-icon>mdi-stop</v-icon>
            </v-btn>
            <v-btn icon @click="iniciarScanner" v-else>
                <v-icon>mdi-play</v-icon>
            </v-btn>
        </v-app-bar>

        <v-main>
            <v-container fluid class="pa-4">
                <v-row>
                    <!-- Cámara y escáner -->
                    <v-col cols="12" md="8">
                        <v-card>
                            <v-card-title>
                                <v-icon class="tw-mr-2">mdi-camera</v-icon>
                                Cámara
                                <v-spacer></v-spacer>
                                <v-chip
                                    :color="scanning ? 'success' : 'error'"
                                    variant="elevated"
                                >
                                    {{ scanning ? 'Activo' : 'Inactivo' }}
                                </v-chip>
                            </v-card-title>
                            <v-card-text>
                                <div class="scanner-container">
                                    <video
                                        ref="videoElement"
                                        class="scanner-video"
                                        autoplay
                                        muted
                                        playsinline
                                    ></video>
                                    <canvas ref="canvasElement" class="tw-hidden"></canvas>

                                    <!-- Overlay de escaneo -->
                                    <div class="scanner-overlay" v-if="scanning">
                                        <div class="scanner-frame">
                                            <div class="scanner-corner scanner-corner-tl"></div>
                                            <div class="scanner-corner scanner-corner-tr"></div>
                                            <div class="scanner-corner scanner-corner-bl"></div>
                                            <div class="scanner-corner scanner-corner-br"></div>
                                        </div>
                                        <div class="scanner-line"></div>
                                    </div>

                                    <!-- Mensaje cuando no está escaneando -->
                                    <div v-if="!scanning" class="scanner-message">
                                        <v-icon size="64" color="grey">mdi-camera-off</v-icon>
                                        <p class="tw-mt-4 tw-text-gray-600">
                                            Cámara desactivada
                                        </p>
                                        <v-btn
                                            color="primary"
                                            @click="iniciarScanner"
                                            class="tw-mt-4"
                                        >
                                            <v-icon start>mdi-play</v-icon>
                                            Iniciar Escáner
                                        </v-btn>
                                    </div>
                                </div>

                                <div class="tw-text-center tw-mt-4">
                                    <p class="tw-text-sm tw-text-gray-600">
                                        Apunta la cámara hacia un código QR para escanearlo automáticamente
                                    </p>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <!-- Panel de resultados -->
                    <v-col cols="12" md="4">
                        <v-card>
                            <v-card-title>
                                <v-icon class="tw-mr-2">mdi-history</v-icon>
                                Códigos Escaneados
                                <v-spacer></v-spacer>
                                <v-badge
                                    :content="scannedCodes.length"
                                    color="success"
                                >
                                    <v-icon>mdi-qrcode</v-icon>
                                </v-badge>
                            </v-card-title>
                            <v-card-text>
                                <div v-if="scannedCodes.length === 0" class="tw-text-center tw-py-8">
                                    <v-icon size="48" color="grey">mdi-qrcode-scan</v-icon>
                                    <p class="tw-text-gray-500 tw-mt-2">
                                        No se han escaneado códigos QR
                                    </p>
                                </div>

                                <div v-else>
                                    <v-list>
                                        <v-list-item
                                            v-for="(codigo, index) in scannedCodes"
                                            :key="index"
                                            class="tw-mb-2"
                                        >
                                            <template #prepend>
                                                <v-avatar color="success">
                                                    <v-icon>mdi-qrcode</v-icon>
                                                </v-avatar>
                                            </template>

                                            <v-list-item-title class="tw-break-all">
                                                {{ codigo }}
                                            </v-list-item-title>

                                            <template #append v-if="codigo.startsWith('http')">
                                                <v-btn
                                                    icon
                                                    size="small"
                                                    color="primary"
                                                    @click="abrirUrl(codigo)"
                                                >
                                                    <v-icon>mdi-open-in-new</v-icon>
                                                </v-btn>
                                            </template>
                                        </v-list-item>
                                    </v-list>

                                    <div class="tw-text-center tw-mt-4">
                                        <v-btn
                                            color="error"
                                            variant="outlined"
                                            @click="limpiarHistorial"
                                        >
                                            <v-icon start>mdi-delete</v-icon>
                                            Limpiar Historial
                                        </v-btn>
                                    </div>
                                </div>
                            </v-card-text>
                        </v-card>

                        <!-- Instrucciones -->
                        <v-card class="tw-mt-4">
                            <v-card-title>
                                <v-icon class="tw-mr-2">mdi-information</v-icon>
                                Instrucciones
                            </v-card-title>
                            <v-card-text>
                                <v-list density="compact">
                                    <v-list-item>
                                        <template #prepend>
                                            <v-icon color="primary">mdi-numeric-1-circle</v-icon>
                                        </template>
                                        <v-list-item-title>
                                            Permite el acceso a la cámara
                                        </v-list-item-title>
                                    </v-list-item>

                                    <v-list-item>
                                        <template #prepend>
                                            <v-icon color="primary">mdi-numeric-2-circle</v-icon>
                                        </template>
                                        <v-list-item-title>
                                            Enfoca el código QR en el marco
                                        </v-list-item-title>
                                    </v-list-item>

                                    <v-list-item>
                                        <template #prepend>
                                            <v-icon color="primary">mdi-numeric-3-circle</v-icon>
                                        </template>
                                        <v-list-item-title>
                                            El código se detectará automáticamente
                                        </v-list-item-title>
                                    </v-list-item>

                                    <v-list-item>
                                        <template #prepend>
                                            <v-icon color="primary">mdi-numeric-4-circle</v-icon>
                                        </template>
                                        <v-list-item-title>
                                            Las URLs se abrirán automáticamente
                                        </v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Botones de acción -->
                <v-row class="tw-mt-4">
                    <v-col cols="12">
                        <div class="tw-text-center">
                            <v-btn
                                href="/dashboard"
                                color="secondary"
                                variant="elevated"
                                class="tw-mr-4"
                            >
                                <v-icon start>mdi-arrow-left</v-icon>
                                Volver al Dashboard
                            </v-btn>

                            <v-btn
                                :color="scanning ? 'error' : 'primary'"
                                variant="elevated"
                                @click="scanning ? detenerScanner() : iniciarScanner()"
                            >
                                <v-icon start>{{ scanning ? 'mdi-stop' : 'mdi-play' }}</v-icon>
                                {{ scanning ? 'Detener' : 'Iniciar' }} Escáner
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <!-- Snackbar -->
        <v-snackbar
            v-model="snackbar"
            :color="snackbarColor"
            :timeout="4000"
            location="top right"
        >
            {{ snackbarMessage }}
            <template #actions>
                <v-btn
                    color="white"
                    variant="text"
                    @click="snackbar = false"
                >
                    Cerrar
                </v-btn>
            </template>
        </v-snackbar>
    </v-app>
</template>

<style scoped>
.scanner-container {
    position: relative;
    width: 100%;
    max-width: 640px;
    margin: 0 auto;
    border-radius: 12px;
    overflow: hidden;
    background: #000;
}

.scanner-video {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 12px;
}

.scanner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.scanner-frame {
    position: relative;
    width: 250px;
    height: 250px;
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-radius: 12px;
}

.scanner-corner {
    position: absolute;
    width: 30px;
    height: 30px;
    border: 3px solid #00ff00;
}

.scanner-corner-tl {
    top: -3px;
    left: -3px;
    border-right: none;
    border-bottom: none;
    border-top-left-radius: 12px;
}

.scanner-corner-tr {
    top: -3px;
    right: -3px;
    border-left: none;
    border-bottom: none;
    border-top-right-radius: 12px;
}

.scanner-corner-bl {
    bottom: -3px;
    left: -3px;
    border-right: none;
    border-top: none;
    border-bottom-left-radius: 12px;
}

.scanner-corner-br {
    bottom: -3px;
    right: -3px;
    border-left: none;
    border-top: none;
    border-bottom-right-radius: 12px;
}

.scanner-line {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%);
    width: 200px;
    height: 2px;
    background: linear-gradient(90deg, transparent, #00ff00, transparent);
    animation: scannerLine 2s linear infinite;
}

@keyframes scannerLine {
    0% {
        top: 25%;
        opacity: 1;
    }
    50% {
        opacity: 1;
    }
    100% {
        top: 75%;
        opacity: 0;
    }
}

.scanner-message {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    background: rgba(0, 0, 0, 0.8);
    padding: 2rem;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

/* Responsive */
@media (max-width: 768px) {
    .scanner-frame {
        width: 200px;
        height: 200px;
    }

    .scanner-line {
        width: 150px;
    }

    .scanner-corner {
        width: 25px;
        height: 25px;
    }
}
</style>
