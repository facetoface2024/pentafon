<script setup lang="ts">
// Componente para mostrar los ejes de conversación del Innovation Day

// Importar las imágenes de los ejes
import eje1Image from '../../images/eje_1.png';
import eje2Image from '../../images/eje_2.png';
import eje3Image from '../../images/eje_3.png';
import eje4Image from '../../images/eje_4.png';
</script>

<template>
    <section class="bg-white py-16">
        <v-container>
            <!-- Título principal -->
            <div class="text-center mb-12">
                <h3 class="subsection-title">
                    Ejes de <span class="text-highlight">conversación</span>
                </h3>
                <p class="subtitle-description">
                    <strong>Cuatro perspectivas clave</strong> que definirán el <strong class="text-highlight">futuro de los negocios</strong>
                </p>
            </div>

            <!-- Grid de ejes -->
            <v-row>
                <v-col cols="12" md="6" class="mb-6">
                    <div class="conversation-topic">
                        <div class="topic-image-header">
                            <img :src="eje1Image" alt="¿Qué será considerado normal?" class="topic-image" />
                        </div>
                        <div class="topic-content">
                            <h4 class="topic-title mb-3">¿Qué será <span class="text-highlight">"normal"</span> en los próximos 5 a 10 años?</h4>
                            <p class="topic-description">
                                <strong>Tendencias</strong> clave que marcarán la nueva realidad de los <strong>negocios, la tecnología y la sociedad.</strong>
                            </p>
                        </div>
                    </div>
                </v-col>

                <v-col cols="12" md="6" class="mb-6">
                    <div class="conversation-topic">
                        <div class="topic-image-header">
                            <img :src="eje2Image" alt="IA generativa, automatización y entornos inmersivos" class="topic-image" />
                        </div>
                        <div class="topic-content">
                            <h4 class="topic-title mb-3"><span class="text-highlight">IA, automatización</span> y entornos inmersivos</h4>
                            <p class="topic-description">
                                Impacto real de <strong>tecnologías emergentes</strong> en los modelos de operación y relación con el cliente.
                            </p>
                        </div>
                    </div>
                </v-col>

                <v-col cols="12" md="6" class="mb-6">
                    <div class="conversation-topic">
                        <div class="topic-image-header">
                            <img :src="eje3Image" alt="Riesgos y regulaciones de la IA" class="topic-image" />
                        </div>
                        <div class="topic-content">
                            <h4 class="topic-title mb-3"><span class="text-highlight">Inteligencia artificial</span>: riesgos y regulaciones</h4>
                            <p class="topic-description">
                                <strong>Sesgos, límites éticos y marcos legales</strong> que ya definen su adopción.
                            </p>
                        </div>
                    </div>
                </v-col>

                <v-col cols="12" md="6" class="mb-6">
                    <div class="conversation-topic">
                        <div class="topic-image-header">
                            <img :src="eje4Image" alt="¿Cómo será tu cliente en 2030?" class="topic-image" />
                        </div>
                        <div class="topic-content">
                            <h4 class="topic-title mb-3"><span class="text-highlight">Cliente 2030</span></h4>
                            <p class="topic-description">
                                <strong>Cómo evolucionan las expectativas</strong>... y cómo superarlas desde hoy.
                            </p>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<style scoped>
.subsection-title {
    font-size: 36px;
    font-weight: 600;
    color: #000;
    margin-bottom: 1rem;
}

.subtitle-description {
    font-size: 18px;
    line-height: 1.6;
    color: #666;
    margin-bottom: 3rem;
}

.text-highlight {
    color: #eb1c2d;
}

.conversation-topic {
    background-color: #f8f8f8;
    border-radius: 8px;
    height: 100%;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    overflow: hidden;
    padding: 0;
}

.conversation-topic:hover {
    background-color: white;
    box-shadow: 0 4px 12px rgba(235, 28, 45, 0.08);
    border-left-color: #eb1c2d;
    transform: translateY(-2px);
}

.topic-image-header {
    width: 100%;
    aspect-ratio: 3/2;
    overflow: hidden;
    position: relative;
}

.topic-image {
    width: 100%;
    height: 100%;
    object-fit: fill;
    object-position: center;
    transition: transform 0.3s ease;
}

.conversation-topic:hover .topic-image {
    transform: scale(1.05);
}

.topic-content {
    padding: 32px;
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

.topic-description strong {
    color: #000;
}

/* Responsive */
@media (max-width: 768px) {
    .subsection-title {
        font-size: 28px;
    }

    .topic-content {
        padding: 24px;
    }

    .topic-title {
        font-size: 18px;
    }

    .topic-description {
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .subsection-title {
        font-size: 24px;
    }

    .topic-content {
        padding: 20px;
    }

    .topic-title {
        font-size: 16px;
    }
}
</style>
