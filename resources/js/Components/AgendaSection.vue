<script setup lang="ts">
const talks = [
    {
        title: 'Regulaciones de IA, sesgos y alucinaciones',
        speaker: 'Manuel Pliego',
        company: 'Microsoft',
        time: '09:00',
        icon: 'mdi-shield-check',
        color: '#2196F3'
    },
    {
        title: 'IA generativa aplicada a CX',
        speaker: 'Por confirmar',
        company: '',
        time: '10:00',
        icon: 'mdi-robot-happy',
        color: '#9C27B0'
    },
    {
        title: 'Omnicanalidad inteligente: automatización sin perder el lado humano',
        speaker: 'Por confirmar',
        company: '',
        time: '11:00',
        icon: 'mdi-account-group',
        color: '#00BCD4'
    },
    {
        title: 'Networking, cocktail',
        speaker: '',
        company: '',
        time: '12:00',
        icon: 'mdi-glass-cocktail',
        color: '#FF5722'
    }
];

const scrollToSection = (sectionId: string) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>

<template>
    <section class="">
        <v-container>
            <div class="text-center mb-12">
                <h2 class="section-title mb-6">
                    <span class="text-highlight">AGENDA</span>
                </h2>
                <p class="section-subtitle">
                    En un mundo donde la innovación es el nuevo estándar, el <strong>Innovation Day</strong> se
                     convierte en un espacio clave para entender cómo las megatendencias están
                      redefiniendo industrias enteras, la experiencia del cliente y las reglas del juego.

                </p>
            </div>

            <v-row>
                <!-- Event Details Card -->
                <v-col cols="12" md="4">
                    <v-card class="event-card" elevation="0">
                        <v-card-text class="pa-8">
                            <v-row>
                                <!-- Columna de Agenda -->
                                <v-col cols="12" sm="6" class="text-center text-sm-left">
                                    <div class="icon-wrapper mb-4">
                                        <v-icon size="40" color="#eb1c2d">mdi-calendar-month</v-icon>
                                    </div>
                                    <div class="event-label">agenda</div>
                                    <div class="event-date">10 de julio</div>
                                    <div class="event-time text-highlight">
                                        <v-icon size="20" class="mr-1">mdi-clock-outline</v-icon>
                                        5:00 pm
                                    </div>
                                </v-col>

                                <!-- Columna de Ubicación -->
                                <v-col cols="12" sm="6" class="text-center text-sm-left mt-6 mt-sm-0">
                                    <div class="icon-wrapper mb-4">
                                        <v-icon size="40" color="#eb1c2d">mdi-map-marker</v-icon>
                                    </div>
                                    <div class="event-label">ubicación</div>
                                    <h3 class="venue-name mb-2">Inspace Narvarte</h3>
                                    <p class="venue-address">
                                        <v-icon size="16" class="mr-1">mdi-city</v-icon>
                                        CDMX
                                    </p>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Talks List -->
                <v-col cols="12" md="8">
                    <div class="talks-list">
                        <div
                            v-for="(talk, index) in talks"
                            :key="index"
                            class="talk-item mb-4"
                        >
                            <div class="talk-icon-time">
                                <div class="talk-icon" :style="{ backgroundColor: talk.color + '20', color: talk.color }">
                                    <v-icon size="24">{{ talk.icon }}</v-icon>
                                </div>
                                <div class="talk-time">{{ talk.time }}</div>
                            </div>

                            <div class="talk-content">
                                <h3 class="talk-title">
                                    {{ talk.title }}
                                </h3>

                                <div v-if="talk.speaker" class="talk-speaker">
                                    <v-icon size="16" class="mr-1">mdi-account</v-icon>
                                    {{ talk.speaker }}<span v-if="talk.company">, {{ talk.company }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA sutil al final -->
                    <div class="text-center mt-8">
                        <p class="cta-text mb-4">
                            <v-icon size="20" color="#eb1c2d" class="mr-2">mdi-alert-circle</v-icon>
                            Cupo limitado • Evento exclusivo para líderes
                        </p>
                        <v-btn
                            color="#eb1c2d"
                            variant="text"
                            @click="scrollToSection('registro')"
                            class="text-capitalize"
                        >
                            Asegura tu participación
                            <v-icon end>mdi-arrow-right</v-icon>
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
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

.section-subtitle {
    font-size: 18px;
    line-height: 1.6;
    color: #666;
    max-width: 800px;
    margin: 0 auto;
}

.event-card {
    background-color: #fafafa;
    border-radius: 16px;
    border: 1px solid #f0f0f0;
}

.icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    background-color: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.event-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #999;
    margin-bottom: 12px;
}

.event-date {
    font-size: 32px;
    font-weight: 700;
    color: #000;
    line-height: 1;
    margin-bottom: 8px;
}

.event-time {
    font-size: 20px;
    font-weight: 400;
    display: flex;
    align-items: center;
    justify-content: center;
}

.venue-name {
    font-size: 18px;
    font-weight: 600;
    color: #000;
}

.venue-address {
    font-size: 14px;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (min-width: 600px) {
    .event-time,
    .venue-address {
        justify-content: flex-start;
    }
}

.talks-list {
    background: transparent;
}

.talk-item {
    background-color: #fafafa;
    border-radius: 12px;
    padding: 24px;
    min-height: auto;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    display: flex;
    align-items: flex-start;
}

.talk-item:hover {
    background-color: #fff;
    border-color: #eb1c2d20;
    box-shadow: 0 4px 16px rgba(235, 28, 45, 0.08);
}

.talk-icon-time {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 24px;
    flex-shrink: 0;
}

.talk-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.talk-time {
    font-size: 16px;
    font-weight: 600;
    color: #eb1c2d;
    white-space: nowrap;
}

.talk-content {
    flex: 1;
    min-width: 0;
}

.talk-title {
    font-size: 20px;
    font-weight: 600;
    color: #000;
    line-height: 1.4;
    margin: 0 0 8px 0;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.talk-speaker {
    font-size: 16px;
    color: #666;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.cta-text {
    font-size: 16px;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    text-align: center;
}

@media (max-width: 599px) {
    .talk-item {
        padding: 16px;
    }

    .talk-icon-time {
        flex-direction: row;
        align-items: center;
        margin-right: 16px;
    }

    .talk-icon {
        margin-bottom: 0;
        margin-right: 12px;
        width: 40px;
        height: 40px;
    }

    .talk-time {
        font-size: 14px;
    }

    .talk-title {
        font-size: 18px;
    }

    .talk-speaker {
        font-size: 14px;
    }
}
</style>
