<script setup lang="ts">
import { ref, nextTick } from 'vue';

// Datos de los speakers
const speakers = ref([
    {
        id: 1,
        name: 'MIGUEL PLIEGO',
        position: 'CARGO/ MICROSOFT',
        photo: null, // Por ahora usaremos placeholder
        bio: 'Experto en inteligencia artificial y transformación digital con más de 15 años de experiencia liderando proyectos innovadores en Microsoft.',
        showTooltip: false,
        tooltipStyle: {},
        tooltipBelow: false
    },
    {
        id: 2,
        name: 'NOMBRE APELLIDO',
        position: 'CARGO/EMPRESA',
        photo: null,
        bio: 'Líder en estrategia de experiencia del cliente, especializado en la implementación de soluciones omnicanal y tecnologías emergentes.',
        showTooltip: false,
        tooltipStyle: {},
        tooltipBelow: false
    },
    {
        id: 3,
        name: 'NOMBRE APELLIDO',
        position: 'CARGO/EMPRESA',
        photo: null,
        bio: 'Pionero en la adopción de IA generativa para mejorar la experiencia del cliente, con un enfoque en ética y regulaciones.',
        showTooltip: false,
        tooltipStyle: {},
        tooltipBelow: false
    }
]);

const toggleTooltip = async (speaker: any, event: MouseEvent) => {
    // Cerrar todos los demás tooltips
    speakers.value.forEach(s => {
        if (s.id !== speaker.id) {
            s.showTooltip = false;
        }
    });

    // Calcular posición del tooltip
    if (!speaker.showTooltip) {
        const button = event.currentTarget as HTMLElement;
        const rect = button.getBoundingClientRect();
        const tooltipWidth = 320;
        const tooltipHeight = 160;
        const padding = 20;
        const gap = 10;

        let left = rect.left + rect.width / 2;
        let top = rect.top - tooltipHeight - gap;

        // Ajustar si se sale por la izquierda
        if (left - tooltipWidth / 2 < padding) {
            left = tooltipWidth / 2 + padding;
        }

        // Ajustar si se sale por la derecha
        if (left + tooltipWidth / 2 > window.innerWidth - padding) {
            left = window.innerWidth - tooltipWidth / 2 - padding;
        }

        // Si se sale por arriba, mostrar abajo
        if (top < padding) {
            top = rect.bottom + gap;
            speaker.tooltipBelow = true;
        } else {
            speaker.tooltipBelow = false;
        }

        speaker.tooltipStyle = {
            top: `${top}px`,
            left: `${left}px`
        };
    }

    speaker.showTooltip = !speaker.showTooltip;
};

const closeAllTooltips = () => {
    speakers.value.forEach(s => {
        s.showTooltip = false;
    });
};
</script>

<template>
    <section class="bg-white">
        <!-- Overlay cuando hay un tooltip abierto -->
        <div
            v-if="speakers.some(s => s.showTooltip)"
            class="tooltip-overlay"
            @click="closeAllTooltips"
        ></div>
        <v-container>
            <div class="text-center mb-12">
                <h2 class="section-title mb-6">
                    <span class="text-highlight">Speakers</span>
                </h2>
            </div>

            <v-row justify="center">
                <v-col
                    v-for="speaker in speakers"
                    :key="speaker.id"
                    cols="12"
                    sm="6"
                    md="4"
                    class="mb-8 d-flex justify-center px-2"
                >
                    <div class="speaker-container">
                        <!-- Imagen del speaker -->
                        <div class="speaker-image-wrapper">
                            <!-- Placeholder de silueta -->
                            <div class="speaker-silhouette">
                                <svg viewBox="0 0 200 250" class="silhouette-svg">
                                    <path d="M100 40 C100 20, 120 0, 100 0 C80 0, 60 20, 60 40 C60 60, 80 80, 100 80 C120 80, 140 60, 140 40 C140 20, 120 0, 100 0 M50 90 C30 90, 20 100, 20 120 L20 220 C20 240, 30 250, 50 250 L150 250 C170 250, 180 240, 180 220 L180 120 C180 100, 170 90, 150 90 Z"
                                          fill="#000" />
                                </svg>
                            </div>

                            <!-- Banda con nombre -->
                            <div class="name-banner">
                                <span class="speaker-name">{{ speaker.name }}</span>
                                <div class="info-button-wrapper">
                                    <button class="info-button" @click="toggleTooltip(speaker, $event)">
                                        <v-icon size="20">mdi-plus</v-icon>
                                    </button>
                                </div>
                            </div>

                            <!-- Cargo/Empresa -->
                            <div class="position-banner">
                                {{ speaker.position }}
                            </div>
                        </div>

                        <!-- Texto SEMBLANZA -->
                        <div class="semblanza-text">SEMBLANZA</div>
                    </div>
                </v-col>
            </v-row>

            <!-- Ejes de conversación -->
            <div class="mt-8">
                <h3 class="subsection-title text-center mb-12">
                    Ejes de <span class="text-highlight">conversación</span>
                </h3>

                <v-row>
                    <v-col cols="12" md="6" class="mb-6">
                        <div class="conversation-topic">
                            <h4 class="topic-title mb-3">¿Qué será considerado "normal" en los próximos 5 a 10 años?</h4>
                            <p class="topic-description">
                                Exploraremos cómo evolucionarán los negocios, la tecnología y la sociedad,
                                y qué implicaciones tendrán estos cambios en la toma de decisiones estratégicas.
                            </p>
                        </div>
                    </v-col>

                    <v-col cols="12" md="6" class="mb-6">
                        <div class="conversation-topic">
                            <h4 class="topic-title mb-3">IA generativa, automatización y entornos inmersivos</h4>
                            <p class="topic-description">
                                Conoce el impacto real de tecnologías como la inteligencia artificial,
                                el metaverso y los asistentes de voz en la experiencia del cliente y
                                la operación de las empresas.
                            </p>
                        </div>
                    </v-col>

                    <v-col cols="12" md="6" class="mb-6">
                        <div class="conversation-topic">
                            <h4 class="topic-title mb-3">Riesgos y regulaciones: lo que sí y lo que no de la IA</h4>
                            <p class="topic-description">
                                Abordaremos los desafíos más relevantes de la inteligencia artificial,
                                desde sesgos y alucinaciones hasta el marco ético y regulatorio que se está gestando.
                            </p>
                        </div>
                    </v-col>

                    <v-col cols="12" md="6" class="mb-6">
                        <div class="conversation-topic">
                            <h4 class="topic-title mb-3">¿Cómo será tu cliente en 2030?</h4>
                            <p class="topic-description">
                                Una mirada hacia el futuro del consumidor: qué espera, cómo se comporta
                                y qué puedes hacer desde hoy para superarlo.
                            </p>
                        </div>
                    </v-col>
                </v-row>
            </div>

            <!-- Tooltips fuera del row para evitar cortes -->
            <teleport to="body">
                <template v-for="speaker in speakers" :key="`tooltip-${speaker.id}`">
                    <v-expand-transition>
                        <div
                            v-if="speaker.showTooltip"
                            class="info-tooltip"
                            :class="{ 'tooltip-below': speaker.tooltipBelow }"
                            :style="speaker.tooltipStyle"
                        >
                            <div class="tooltip-arrow"></div>
                            <div class="tooltip-content">
                                <button class="close-button" @click="speaker.showTooltip = false">
                                    <v-icon size="18">mdi-close</v-icon>
                                </button>
                                <p class="tooltip-text">{{ speaker.bio }}</p>
                            </div>
                        </div>
                    </v-expand-transition>
                </template>
            </teleport>
        </v-container>
    </section>
</template>

<style scoped>
.section-title {
    font-size: 48px;
    font-weight: 700;
    letter-spacing: 2px;
    color: #000;
}

.text-highlight {
    color: #eb1c2d;
}

.subsection-title {
    font-size: 36px;
    font-weight: 600;
    color: #000;
}

.speaker-container {
    position: relative;
    width: 100%;
    max-width: 392px;
    text-align: center;
    margin: 0 auto;
}

.speaker-image-wrapper {
    position: relative;
    width: 100%;
    height: 490px;
    background-color: #f5f5f5;
    border-radius: 12px;
    overflow: visible;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.speaker-silhouette {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 56px;
    border-radius: 12px;
    overflow: hidden;
    background-color: #f5f5f5;
}

.silhouette-svg {
    width: 100%;
    height: 100%;
    max-width: 280px;
}

.name-banner {
    position: absolute;
    bottom: 84px;
    left: 0;
    right: 0;
    background-color: #eb1c2d;
    color: white;
    padding: 16px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 0 50px 50px 0;
    margin-right: 42px;
    z-index: 10;
}

.speaker-name {
    font-size: 18px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.info-button {
    background-color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.info-button:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.info-button .v-icon {
    color: #eb1c2d;
}

.info-button-wrapper {
    position: relative;
    z-index: 1001;
}

.position-banner {
    position: absolute;
    bottom: 28px;
    left: 0;
    right: 0;
    background-color: white;
    color: #333;
    padding: 11px 28px;
    font-size: 16px;
    font-weight: 500;
    border-radius: 0 50px 50px 0;
    margin-right: 70px;
    z-index: 10;
}

.semblanza-text {
    margin-top: 34px;
    padding: 16px 34px;
    background-color: #e5e5e5;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    color: #666;
    letter-spacing: 1px;
}

.info-tooltip {
    position: fixed;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
    width: 320px;
    z-index: 1001;
    animation: tooltipFadeIn 0.3s ease;
    transform: translateX(-50%);
}

@keyframes tooltipFadeIn {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

.tooltip-arrow {
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 10px solid white;
    filter: drop-shadow(0 3px 3px rgba(0, 0, 0, 0.1));
}

.tooltip-content {
    padding: 20px;
    position: relative;
}

.close-button {
    position: absolute;
    top: 8px;
    right: 8px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    transition: background-color 0.2s ease;
}

.close-button:hover {
    background-color: #f5f5f5;
}

.close-button .v-icon {
    color: #666;
}

.tooltip-text {
    font-size: 14px;
    line-height: 1.6;
    color: #333;
    margin: 0;
    padding-right: 20px;
}

.conversation-topic {
    padding: 32px;
    background-color: #f8f8f8;
    border-radius: 8px;
    height: 100%;
    transition: all 0.3s ease;
}

.conversation-topic:hover {
    background-color: white;
    box-shadow: 0 4px 12px rgba(235, 28, 45, 0.08);
}

.topic-title {
    font-size: 20px;
    font-weight: 600;
    color: #000;
    line-height: 1.3;
    border-left: 3px solid #eb1c2d;
    padding-left: 16px;
}

.topic-description {
    font-size: 16px;
    line-height: 1.6;
    color: #666;
}

.tooltip-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.3);
    z-index: 999;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.info-tooltip.tooltip-below .tooltip-arrow {
    top: -8px;
    bottom: auto;
    border-top: none;
    border-bottom: 10px solid white;
}

/* Prevenir encimado en resoluciones intermedias */
@media (max-width: 1024px) and (min-width: 769px) {
    .speaker-container {
        max-width: 320px;
    }

    .speaker-image-wrapper {
        height: 400px;
    }

    .speaker-silhouette {
        padding: 44px;
    }

    .silhouette-svg {
        max-width: 230px;
    }

    .name-banner {
        bottom: 68px;
        padding: 12px 20px;
        margin-right: 32px;
    }

    .position-banner {
        bottom: 22px;
        padding: 8px 20px;
        margin-right: 56px;
    }

    .speaker-name {
        font-size: 16px;
    }

    .info-button {
        width: 36px;
        height: 36px;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .speaker-container {
        max-width: 336px;
    }

    .speaker-image-wrapper {
        height: 420px;
    }

    .speaker-silhouette {
        padding: 48px;
    }

    .silhouette-svg {
        max-width: 240px;
    }

    .name-banner {
        bottom: 72px;
        padding: 14px 24px;
        margin-right: 36px;
    }

    .position-banner {
        bottom: 24px;
        padding: 10px 24px;
        margin-right: 60px;
    }
}

@media (max-width: 599px) {
    .speaker-container {
        max-width: 280px;
    }

    .speaker-image-wrapper {
        height: 350px;
    }

    .speaker-silhouette {
        padding: 40px;
    }

    .silhouette-svg {
        max-width: 200px;
    }

    .name-banner {
        bottom: 60px;
        padding: 10px 16px;
        margin-right: 20px;
    }

    .speaker-name {
        font-size: 14px;
    }

    .info-button {
        width: 32px;
        height: 32px;
    }

    .position-banner {
        bottom: 20px;
        padding: 6px 16px;
        font-size: 12px;
        margin-right: 30px;
    }

    .semblanza-text {
        margin-top: 24px;
        padding: 12px 24px;
        font-size: 14px;
    }

    .info-tooltip {
        width: 280px;
        max-width: calc(100vw - 40px);
    }
}

/* Ya no necesitamos estos overrides */
section {
    position: relative;
}
</style>
