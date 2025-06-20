<script setup lang="ts">
import { onMounted, ref } from 'vue';
import pentafonLogo from '@/../images/logo.svg';

const props = defineProps<{
    isLoading: boolean;
}>();

const emit = defineEmits<{
    loaded: [];
}>();

const progress = ref(0);

// Generar posiciones para los nodos de la red
const getNodeStyle = (index: number) => {
    const angle = (index / 12) * Math.PI * 2;
    const radius = 25 + (index % 3) * 15; // Diferentes radios para variedad
    const x = 50 + Math.cos(angle) * radius;
    const y = 50 + Math.sin(angle) * radius;

    return {
        left: `${x}%`,
        top: `${y}%`,
        animationDelay: `${index * 0.2}s`
    };
};

// Simular progreso de carga
const simulateProgress = () => {
    const interval = setInterval(() => {
        progress.value += Math.random() * 15 + 5;
        if (progress.value >= 100) {
            progress.value = 100;
            clearInterval(interval);
            setTimeout(() => {
                emit('loaded');
            }, 500);
        }
    }, 250);
};

onMounted(() => {
    simulateProgress();
});
</script>

<template>
    <Transition name="fade">
        <div v-if="isLoading" class="preloader">
            <!-- Fondo tecnológico minimalista -->
            <div class="tech-background">
                <!-- Red de conexiones -->
                <div class="network-grid">
                    <div class="network-node" v-for="i in 12" :key="i" :style="getNodeStyle(i)">
                        <div class="node-dot"></div>
                        <div class="connection-lines">
                            <div class="line line-1"></div>
                            <div class="line line-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Ondas de pulso -->
                <div class="pulse-waves">
                    <div class="pulse-ring" v-for="i in 3" :key="i" :style="{ animationDelay: `${i * 0.6}s` }"></div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="logo-container">
                <img
                    :src="pentafonLogo"
                    alt="Pentafon Logo"
                    class="logo"
                />
            </div>

            <div class="loader-content">
                <div class="progress-container">
                    <div class="progress-track">
                        <div class="progress-bar" :style="{ width: `${progress}%` }"></div>
                        <div class="progress-glow" :style="{ width: `${progress}%` }"></div>
                    </div>
                </div>
                <p class="loading-text">Cargando Innovation Day 2025...</p>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 9999;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* Fondo tecnológico */
.tech-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.03;
    z-index: 1;
}

/* Red de conexiones */
.network-grid {
    position: absolute;
    width: 100%;
    height: 100%;
}

.network-node {
    position: absolute;
    width: 6px;
    height: 6px;
    opacity: 0;
    animation: nodeAppear 3s ease-in-out infinite;
}

.node-dot {
    width: 100%;
    height: 100%;
    background: #e4233c;
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(228, 35, 60, 0.3);
}

.connection-lines {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.line {
    position: absolute;
    height: 1px;
    background: linear-gradient(90deg, transparent, #e4233c, transparent);
    transform-origin: left center;
    opacity: 0;
    animation: lineGrow 4s ease-in-out infinite;
}

.line-1 {
    width: 60px;
    transform: rotate(45deg);
    animation-delay: 0.5s;
}

.line-2 {
    width: 40px;
    transform: rotate(-30deg);
    animation-delay: 1s;
}

/* Ondas de pulso */
.pulse-waves {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.pulse-ring {
    position: absolute;
    width: 300px;
    height: 300px;
    border: 1px solid rgba(228, 35, 60, 0.1);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: pulse 2s ease-out infinite;
}

/* Contenido principal */
.logo-container {
    position: relative;
    z-index: 10;
}

.logo {
    width: 180px;
    height: auto;
    animation: logoFloat 3s ease-in-out infinite;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.05));
}

.loader-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    text-align: center;
}

/* Barra de progreso mejorada */
.progress-container {
    width: 240px;
    margin: 40px auto 16px;
}

.progress-track {
    position: relative;
    width: 100%;
    height: 3px;
    background: rgba(228, 35, 60, 0.1);
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(90deg, #e4233c 0%, #a91832 100%);
    border-radius: 3px;
    transition: width 0.4s ease;
}

.progress-glow {
    position: absolute;
    top: -1px;
    left: 0;
    height: 5px;
    background: linear-gradient(90deg, transparent, rgba(228, 35, 60, 0.3), transparent);
    border-radius: 3px;
    transition: width 0.4s ease;
    filter: blur(2px);
}

.loading-text {
    color: #4a5568;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.8px;
    opacity: 0.8;
    animation: textPulse 2s ease-in-out infinite;
}

/* Animaciones */
@keyframes nodeAppear {
    0%, 100% {
        opacity: 0;
        transform: scale(0.8);
    }
    50% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes lineGrow {
    0%, 100% {
        opacity: 0;
        transform: scaleX(0);
    }
    50% {
        opacity: 0.6;
        transform: scaleX(1);
    }
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0.4;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0;
    }
}

@keyframes logoFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

@keyframes textPulse {
    0%, 100% {
        opacity: 0.8;
    }
    50% {
        opacity: 1;
    }
}

/* Transiciones */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.6s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .logo {
        width: 160px;
    }

    .progress-container {
        width: 200px;
        margin: 32px auto 12px;
    }

    .loader-content {
        top: 62%;
    }

    .pulse-ring {
        width: 200px;
        height: 200px;
    }
}

@media (max-width: 480px) {
    .logo {
        width: 140px;
    }

    .progress-container {
        width: 180px;
    }

    .loading-text {
        font-size: 12px;
    }
}
</style>
