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
                        <h3 class="location-title mb-2"><span class="text-highlight">INSPACE</span> CDMX</h3>
                        <p class="location-address">
                            <strong>C. J. Enrique Pestalozzi 35</strong>, Piedad Narvarte,<br>
                            Benito Juárez, <strong>03000 Ciudad de México, CDMX</strong>
                        </p>

                        <!-- Embed del mapa -->
                        <div class="map-container mt-4">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3763.215653287468!2d-99.15787549999999!3d19.4030861!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff007394e263%3A0xc74be39f7ae858d1!2sInSpace!5e0!3m2!1ses!2smx!4v1750438587174!5m2!1ses!2smx"
                                width="100%"
                                height="500"
                                style="border:0; border-radius: 8px;"
                                allowfullscreen
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>


                </v-col>

                <!-- Formulario de registro -->
                <v-col cols="12" md="6">
                    <div class="register-form-wrapper">
                        <h2 class="form-title mb-4">
                            Pre-<span class="text-highlight">regístrate</span>
                        </h2>
                        <p class="form-subtitle mb-8">
                            El evento es <strong class="text-highlight">gratuito</strong>, pero los espacios son <strong>limitados</strong>.<br>
                            <strong>Completa el formulario</strong> para iniciar tu registro.<br>
                            Muy pronto recibirás <strong>más información</strong> para <strong class="text-highlight">finalizar tu participación</strong>.
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

.map-container {
    position: relative;
    width: 100%;
    height: 430px;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.map-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
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
    transition: all 0.3s ease;
}

.register-form-wrapper:hover {
    box-shadow: 0 4px 16px rgba(235, 28, 45, 0.06);
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

.form-subtitle strong {
    color: #000;
}

.location-address strong {
    color: #000;
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

    .map-container {
        height: 400px;
        margin-top: 24px;
    }

    .location-card {
        padding: 24px;
    }
}
</style>
