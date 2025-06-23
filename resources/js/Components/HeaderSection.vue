<script setup lang="ts">
import { ref } from 'vue';
import logoSvg from '../../images/logo.svg';
import innovationSvg from '../../images/innovation.svg';

const drawer = ref(false);

const scrollToSection = (sectionId: string) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
    // Cerrar el drawer en móvil después de hacer clic
    drawer.value = false;
};
</script>

<template>
    <v-app-bar
        fixed
        flat
        color="white"
        height="64"
        :elevation="1"
    >
        <v-container fluid class="px-4">
            <div class="header-content">
                <!-- Logo Pentafon -->
                <div class="logo-pentafon-wrapper">
                    <img :src="logoSvg" alt="Pentafon" class="logo-pentafon" />
                </div>

                <!-- Logo Innovation Day (centrado en desktop y tablet) -->
                <div class="logo-innovation-wrapper d-none d-md-flex">
                    <img :src="innovationSvg" alt="Innovation Day 2025" class="logo-innovation" />
                </div>

                <!-- Navigation Desktop -->
                <div class="navigation-wrapper d-none d-md-flex">
                 <!--    <v-btn
                        variant="text"
                        @click="scrollToSection('agenda')"
                        class="text-grey-darken-3"
                        size="small"
                    >
                        Agenda
                    </v-btn>
                    <v-btn
                        variant="text"
                        @click="scrollToSection('speakers')"
                        class="text-grey-darken-3"
                        size="small"
                    >
                        Speakers
                    </v-btn> -->
                    <v-btn
                        color="#eb1c2d"
                        variant="flat"
                        rounded="pill"
                        @click="scrollToSection('registro')"
                        class="px-4 text-white"
                        size="small"
                    >
                        Prerregístrate
                    </v-btn>
                </div>

                <!-- Logo Innovation Day móvil (solo en móvil) -->
                <div class="logo-innovation-mobile d-flex d-md-none">
                    <img :src="innovationSvg" alt="Innovation Day 2025" class="logo-innovation-small" />
                </div>

                <!-- Mobile menu button -->
                <v-app-bar-nav-icon
                    class="d-md-none"
                    @click="drawer = !drawer"
                />
            </div>
        </v-container>
    </v-app-bar>

    <!-- Navigation Drawer para móvil -->
    <v-navigation-drawer
        v-model="drawer"
        location="right"
        temporary
        width="280"
    >
        <v-list>
            <v-list-item>
                <div class="drawer-header">
                    <img :src="logoSvg" alt="Pentafon" class="drawer-logo" />
                </div>
            </v-list-item>

            <v-divider class="my-4"></v-divider>

            <v-list-item
                @click="scrollToSection('inicio')"
                prepend-icon="mdi-home"
            >
                <v-list-item-title>Inicio</v-list-item-title>
            </v-list-item>

      <!--       <v-list-item
                @click="scrollToSection('agenda')"
                prepend-icon="mdi-calendar"
            >
                <v-list-item-title>Agenda</v-list-item-title>
            </v-list-item>

            <v-list-item
                @click="scrollToSection('speakers')"
                prepend-icon="mdi-account-group"
            >
                <v-list-item-title>Speakers</v-list-item-title>
            </v-list-item> -->

            <v-list-item
                @click="scrollToSection('registro')"
                prepend-icon="mdi-form-textbox"
            >
                <v-list-item-title>Preregístro</v-list-item-title>
            </v-list-item>
        </v-list>

        <template v-slot:append>
            <div class="pa-4">
                <v-btn
                    color="#eb1c2d"
                    block
                    rounded="pill"
                    @click="scrollToSection('registro')"
                >
                    Prerregístrate ahora
                </v-btn>
            </div>
        </template>
    </v-navigation-drawer>
</template>

<style scoped>
.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 100%;
}

.logo-pentafon-wrapper {
    flex-shrink: 0;
    display: flex;
    align-items: center;
}

.logo-pentafon {
    height: 32px;
    width: auto;
}

.logo-innovation-wrapper {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    pointer-events: none; /* Para que no interfiera con los clics */
}

.logo-innovation {
    height: 48px;
    width: auto;
}

.logo-innovation-mobile {
    flex: 1;
    justify-content: center;
    align-items: center;
    margin: 0 16px;
}

.logo-innovation-small {
    height: 50px;
    margin-top: 10px;
    width: auto;
    max-width: 100%;
}

.navigation-wrapper {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.drawer-header {
    padding: 16px 0;
}

.drawer-logo {
    height: 28px;
    width: auto;
}

/* Media queries para evitar superposiciones */
@media (min-width: 960px) and (max-width: 1279px) {
    /* Tablets y pantallas medianas */
    .logo-innovation {
        height: 60px;
    }

    .navigation-wrapper {
        gap: 4px;
    }

    .navigation-wrapper .v-btn {
        padding: 0 12px;
    }
}

@media (min-width: 1280px) and (max-width: 1599px) {
    /* Pantallas medianas-grandes */
    .logo-innovation {
        height: 60px;
    }
}

@media (min-width: 1600px) {
    /* Pantallas grandes */
    .logo-innovation {
        height: 60px;
    }

    .navigation-wrapper {
        gap: 16px;
    }

    .navigation-wrapper .v-btn {
        padding: 0 16px;
    }
}

/* Ajustes para móvil */
@media (max-width: 599px) {
    .logo-pentafon {
        height: 28px;
    }

    .header-content {
        padding: 0;
    }
}

/* Para tablets pequeñas */
@media (min-width: 600px) and (max-width: 959px) {
    .logo-pentafon {
        height: 28px;
    }
}

/* Asegurar que en pantallas muy pequeñas no se encimen */
@media (max-width: 959px) {
    .logo-innovation-wrapper {
        display: none !important;
    }
}
</style>
