<script setup lang="ts">
// Componente para mostrar los ejes de conversación del Innovation Day
</script>

<template>
    <section class="bg-white py-16">
        <v-container>
            <!-- Título principal -->
            <div class="text-center mb-12">
                <h3 class="subsection-title">
                    Ejes de <span class="text-highlight">conversación</span>
                </h3>
            </div>

            <!-- Grid de ejes -->
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
        </v-container>
    </section>
</template>

<style scoped>
.subsection-title {
    font-size: 36px;
    font-weight: 600;
    color: #000;
    margin-bottom: 3rem;
}

.text-highlight {
    color: #eb1c2d;
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

/* Responsive */
@media (max-width: 768px) {
    .subsection-title {
        font-size: 28px;
    }

    .conversation-topic {
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

    .conversation-topic {
        padding: 20px;
    }

    .topic-title {
        font-size: 16px;
    }
}
</style>
