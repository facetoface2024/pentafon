<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import HeaderSection from '@/Components/HeaderSection.vue';
import HeroIntro from '@/Components/HeroIntro.vue';
import AgendaSection from '@/Components/AgendaSection.vue';
import SpeakersSection from '@/Components/SpeakersSection.vue';
import Ejes from '@/Components/Ejes.vue';
import RegisterSection from '@/Components/RegisterSection.vue';
import PrivacyNotice from '@/Components/PrivacyNotice.vue';
import Preloader from '@/Components/Preloader.vue';

// Las imágenes ahora están en public/images

const isLoading = ref(true);

const handleLoaded = () => {
    isLoading.value = false;
};

const scrollToRegistro = () => {
    const element = document.getElementById('registro');
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};

// Configuración SEO
const seoConfig = {
    title: 'Innovation Day 2025 - Pentafon & Microsoft | Evento Presencial CDMX',
    description: 'Únete al Innovation Day 2025 donde Pentafon y Microsoft se unen para explorar las megatendencias tecnológicas que están redefiniendo los negocios. Evento presencial en CDMX - 10 Julio 2025, 5:00 PM. Cupo limitado.',
    keywords: 'Innovation Day 2025, Pentafon, Microsoft, tecnología, innovación, CDMX, evento presencial, megatendencias tecnológicas, transformación digital, liderazgo empresarial',
    author: 'Pentafon',
    ogImage: window.location.origin + '/images/banner.webp',
    ogUrl: window.location.href,
    favicon: '/images/favicon.png'
};

onMounted(() => {
    // También ocultar el preloader si todo está cargado después de 3 segundos
    setTimeout(() => {
        isLoading.value = false;
    }, 3000);

    // Insertar datos estructurados JSON-LD
    const jsonLd = {
        "@context": "https://schema.org",
        "@type": "Event",
        "name": "Innovation Day 2025",
        "description": "Pentafon y Microsoft se unen para explorar las megatendencias tecnológicas que están redefiniendo los negocios",
        "startDate": "2025-07-10T17:00:00-06:00",
        "endDate": "2025-07-10T21:00:00-06:00",
        "eventStatus": "https://schema.org/EventScheduled",
        "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
        "location": {
            "@type": "Place",
            "name": "InSpace CDMX",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "C. J. Enrique Pestalozzi 35",
                "addressLocality": "Piedad Narvarte, Benito Juárez",
                "postalCode": "03000",
                "addressRegion": "CDMX",
                "addressCountry": "MX"
            }
        },
        "organizer": {
            "@type": "Organization",
            "name": "Pentafon",
            "url": "https://pentafon.com"
        },
        "sponsor": {
            "@type": "Organization",
            "name": "Microsoft",
            "url": "https://microsoft.com"
        },
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "MXN",
            "availability": "https://schema.org/LimitedAvailability",
            "url": `${window.location.href}#registro`
        },
        "image": `${window.location.origin}/images/banner.webp`
    };

    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(jsonLd);
    document.head.appendChild(script);
});
</script>

<template>
    <Head>
        <!-- Título de la página -->
        <title>{{ seoConfig.title }}</title>

        <!-- Meta tags básicas -->
        <meta name="description" :content="seoConfig.description" />
        <meta name="keywords" :content="seoConfig.keywords" />
        <meta name="author" :content="seoConfig.author" />
        <meta name="robots" content="index, follow" />
        <meta name="language" content="Spanish" />
        <meta name="revisit-after" content="7 days" />

        <!-- Viewport y compatibilidad -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- Favicon -->
        <link rel="icon" type="image/png" :href="seoConfig.favicon" />
        <link rel="shortcut icon" type="image/png" :href="seoConfig.favicon" />
        <link rel="apple-touch-icon" :href="seoConfig.favicon" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="seoConfig.ogUrl" />
        <meta property="og:title" :content="seoConfig.title" />
        <meta property="og:description" :content="seoConfig.description" />
        <meta property="og:image" :content="seoConfig.ogImage" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:site_name" content="Innovation Day 2025" />
        <meta property="og:locale" content="es_MX" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:url" :content="seoConfig.ogUrl" />
        <meta name="twitter:title" :content="seoConfig.title" />
        <meta name="twitter:description" :content="seoConfig.description" />
        <meta name="twitter:image" :content="seoConfig.ogImage" />
        <meta name="twitter:creator" content="@Pentafon" />



        <!-- Preconnect para performance -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />

        <!-- Preload recursos críticos -->
        <link rel="preload" href="/images/logo.svg" as="image" type="image/svg+xml" />
        <link rel="preload" href="/images/innovation.svg" as="image" type="image/svg+xml" />
        <link rel="preload" href="/images/frase-2.png" as="image" type="image/png" />
        <link rel="preload" href="/images/background_web.jpg" as="image" type="image/jpeg" media="(min-width: 960px)" />

        <!-- Canonical URL -->
        <link rel="canonical" :href="seoConfig.ogUrl.split('#')[0]" />

        <!-- Hreflang para español -->
        <link rel="alternate" hreflang="es-mx" :href="seoConfig.ogUrl" />
        <link rel="alternate" hreflang="es" :href="seoConfig.ogUrl" />

        <!-- Theme color -->
        <meta name="theme-color" content="#eb1c2d" />
        <meta name="msapplication-TileColor" content="#eb1c2d" />

        <!-- Información adicional del evento -->
        <meta name="event:start_date" content="2025-07-10T17:00:00-06:00" />
        <meta name="event:end_date" content="2025-07-10T21:00:00-06:00" />
        <meta name="event:location" content="InSpace CDMX, Ciudad de México" />
        <meta name="event:organizer" content="Pentafon" />
    </Head>

    <v-app>
        <Preloader :is-loading="isLoading" @loaded="handleLoaded" />
        <HeaderSection />
        <v-main>
            <HeroIntro id="inicio" />
        <!--    <AgendaSection id="agenda" />
            <SpeakersSection id="speakers" />  -->
            <Ejes id="ejes" />
            <RegisterSection id="registro" />
        </v-main>
        <PrivacyNotice />

        <!-- Botón flotante de fecha y hora -->
        <v-btn
            class="floating-event-btn"
            color="#eb1c2d"
            @click="scrollToRegistro"
            elevation="6"
            rounded="pill"
        >
            <div class="event-btn-content">
                <div class="event-date">📅 10 Julio 2025</div>
                <div class="event-time">🕔 5:00 PM</div>
            </div>
        </v-btn>
    </v-app>
</template>

<style scoped>
:deep(.v-main) {
    padding-top: 64px;
}

.floating-event-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(235, 28, 45, 0.3);
    border: none;
    padding: 15px 20px !important;
    height: auto !important;
    min-height: 75px;
}

.floating-event-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(235, 28, 45, 0.4);
}

.event-btn-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    line-height: 1.2;
}

.event-date {
    font-size: 18px;
    font-weight: 600;
    color: white;
    margin-bottom: 2px;
}

.event-time {
    font-size: 15px;
    font-weight: 500;
    color: white;
    opacity: 0.95;
}

/* Estilos para móvil */
@media (max-width: 768px) {
    .floating-event-btn {
        bottom: 15px;
        right: 15px;
        padding: 12px 16px !important;
        min-height: 63px;
    }

    .event-date {
        font-size: 15px;
    }

    .event-time {
        font-size: 13px;
    }
}

/* Para pantallas muy pequeñas */
@media (max-width: 480px) {
    .floating-event-btn {
        bottom: 10px;
        right: 10px;
        padding: 9px 14px !important;
        min-height: 58px;
    }

    .event-date {
        font-size: 14px;
    }

    .event-time {
        font-size: 12px;
    }
}
</style>
