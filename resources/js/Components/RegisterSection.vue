<script setup lang="ts">
import { onMounted } from 'vue';

// Función para cargar el formulario de HubSpot
const loadHubSpotForm = () => {
    // Primero cargamos el script de HubSpot si no está cargado
    if (!window.hbspt) {
        const script = document.createElement('script');
        script.src = '//js.hsforms.net/forms/embed/v2.js';
        script.charset = 'utf-8';
        script.onload = () => {
            // Una vez cargado el script, creamos el formulario
            if (window.hbspt) {
                window.hbspt.forms.create({
                    portalId: "7462595",
                    formId: "97bd5ccc-6e8b-4595-afac-77bd082a343c",
                    region: "na1",
                    target: '#hubspot-form'
                });
            }
        };
        document.head.appendChild(script);
    } else {
        // Si ya está cargado, creamos el formulario directamente
        window.hbspt.forms.create({
            portalId: "7462595",
            formId: "97bd5ccc-6e8b-4595-afac-77bd082a343c",
            region: "na1",
            target: '#hubspot-form'
        });
    }
};

onMounted(() => {
    loadHubSpotForm();
});
</script>

<template>
    <section class="">
        <v-container>
            <v-row>
                <!-- Información y ubicación -->
                <v-col cols="12" md="6" class="mb-8">
                    <h2 class="section-title mb-8">
                        <span class="text-highlight">Ubicación</span>
                    </h2>

                    <div class="location-card mb-8">
                        <h3 class="location-title mb-2">INSPACE CDMX</h3>
                        <p class="location-address">
                            C. J. Enrique Pestalozzi 35, Piedad Narvarte,<br>
                            Benito Juárez, 03000 Ciudad de México, CDMX
                        </p>
                        <v-btn
                            color="#eb1c2d"
                            variant="outlined"
                            class="mt-4"
                            href="https://maps.app.goo.gl/QnujJdDK3PDYQpBK6"
                            target="_blank"
                        >
                            Ver mapa
                        </v-btn>
                    </div>

                    <!-- Información adicional -->
                    <v-expansion-panels class="info-panels">
                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                Nosotros
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                Pentafon es una empresa líder en soluciones de experiencia al cliente,
                                especializada en la implementación de tecnologías innovadoras para
                                transformar la forma en que las empresas se conectan con sus clientes.
                            </v-expansion-panel-text>
                        </v-expansion-panel>

                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                Qué hacemos
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                Desarrollamos e implementamos soluciones de contact center omnicanal,
                                inteligencia artificial aplicada a CX, automatización de procesos y
                                análisis avanzado de datos para mejorar la experiencia del cliente.
                            </v-expansion-panel-text>
                        </v-expansion-panel>

                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                Reconocimientos
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                • Partner Gold de Microsoft<br>
                                • Líder en el Cuadrante Mágico de Gartner para CCaaS<br>
                                • Premio a la Innovación en CX 2024<br>
                                • Certificación ISO 27001
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-col>

                <!-- Formulario de registro -->
                <v-col cols="12" md="6">
                    <div class="register-form-wrapper">
                        <h2 class="form-title mb-4">
                            Pre-<span class="text-highlight">regístrate</span>
                        </h2>
                        <p class="form-subtitle mb-8">
                            El evento es gratuito, pero los espacios son limitados.<br>
                            Completa el formulario para iniciar tu registro.<br>
                            Muy pronto recibirás más información para finalizar tu participación.
                        </p>

                        <!-- Contenedor para el formulario de HubSpot -->
                        <div id="hubspot-form"></div>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<style scoped>
.section-title {
    font-size: 36px;
    font-weight: 700;
    color: #000;
}

.text-highlight {
    color: #eb1c2d;
}

.location-card {
    padding: 32px;
    background-color: #f8f8f8;
    border-radius: 8px;
    border-left: 4px solid #eb1c2d;
}

.location-title {
    font-size: 24px;
    font-weight: 600;
    color: #000;
}

.location-address {
    font-size: 16px;
    line-height: 1.6;
    color: #666;
}

.info-panels {
    background: transparent;
}

.info-panels :deep(.v-expansion-panel) {
    background-color: #f8f8f8;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.info-panels :deep(.v-expansion-panel--active) {
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(235, 28, 45, 0.08);
}

.info-panels :deep(.v-expansion-panel-title) {
    font-weight: 600;
    color: #000;
}

.register-form-wrapper {
    background-color: #f8f8f8;
    padding: 48px;
    border-radius: 12px;
}

.form-title {
    font-size: 36px;
    font-weight: 700;
    color: #000;
}

.form-subtitle {
    font-size: 16px;
    line-height: 1.6;
    color: #666;
}

/* Estilos para el formulario de HubSpot */
#hubspot-form :deep(.hs-form-field label) {
    color: #333 !important;
    font-weight: 500 !important;
}

#hubspot-form :deep(.hs-input) {
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
    padding: 12px !important;
    font-size: 16px !important;
    transition: all 0.3s ease !important;
}

#hubspot-form :deep(.hs-input:focus) {
    border-color: #eb1c2d !important;
    outline: none !important;
}

#hubspot-form :deep(.hs-button) {
    background-color: #eb1c2d !important;
    color: white !important;
    border: none !important;
    padding: 16px 32px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    border-radius: 50px !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    width: 100% !important;
}

#hubspot-form :deep(.hs-button:hover) {
    background-color: #d11926 !important;
    transform: translateY(-2px) !important;
}

@media (max-width: 960px) {
    .register-form-wrapper {
        padding: 32px 24px;
    }
}
</style>
