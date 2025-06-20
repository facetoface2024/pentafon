import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createI18n } from 'vue-i18n';
import { createVuetify } from 'vuetify';
import { es } from 'vuetify/locale'; // Traducción en español de Vuetify
import '@mdi/font/css/materialdesignicons.css'; // Para los iconos
// Configuración de i18n para Vue (opcional si quieres más control de localización)
const i18n = createI18n({
    locale: 'es-MX',
    messages: {
        es: es,
    },
});

// Configuración de Vuetify
const vuetify = createVuetify({
    theme: {
        defaultTheme: 'light',
        themes: {
          light: {
            dark: false,
            colors: {
              primary: '#1976D2',
              secondary: '#424242',
            },
          },
        },
      },
      icons: {
          defaultSet: 'mdi',
        },
        locale: {
          locale: 'es',
          fallback: 'es',
          messages: { es },
        },
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n) // Usar i18n si es necesario
            .use(vuetify) // Usar Vuetify
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
