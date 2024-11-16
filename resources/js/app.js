import "../css/app.css";
import "./bootstrap";

// Vuetify
import "@mdi/font/css/materialdesignicons.css";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { en, ru, uk } from "vuetify/locale";
import "vuetify/styles";

// Core Vue and inertiajs
import { createInertiaApp, router } from "@inertiajs/vue3";
import { createApp, h } from "vue";

// Helpers and plugins
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { Settings } from "luxon";
import { createPinia } from "pinia";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

// Stores and custom plugins
import { useUserStore } from "@/Stores";
import useSession from "@/Composables/useSession";
import useLink from "@/Plugins/Link";

// Toast
import ToastPlugin, { useToast } from "vue-toast-notification";
import "vue-toast-notification/dist/theme-default.css";

const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: "mdi",
    },
    locale: {
        locale: import.meta.env.VITE_APP_LOCALE,
        messages: { en, uk, ru },
    },
});

Settings.defaultLocale = import.meta.env.VITE_APP_LOCALE;

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

function createAppInstance({ App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(createPinia())
        .use(ZiggyVue)
        .use(vuetify)
        .use(ToastPlugin)
        .use(useLink);

    app.config.globalProperties.$appName = appName;

    return app;
}

function setupEventListeners() {
    const $toast = useToast();

    router.on("success", (event) => {
        setupStores(event.detail.page.props);
        const status = event.detail.page.props?.status;

        if (status) {
            $toast.open({
                message: status.message,
                type: status.type || "default",
                duration: 5000,
            });
        }
    });
}

function setupStores(props) {
    const { user = null } = props.auth ?? {};

    const userStore = useUserStore();

    userStore.setUser(user);
}

function toggleTheme(vuetify) {
    const themeMode = useSession.get("theme", "light");
    vuetify.theme.global.name = themeMode;

    if (themeMode === "dark") {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
}

createInertiaApp({
    title: (title) => `${appName} - ${title}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const appInstance = createAppInstance({ App, props, plugin });

        setupStores(props.initialPage.props);
        setupEventListeners();

        const mountedApp = appInstance.mount(el);
        toggleTheme(mountedApp.$vuetify);

        return mountedApp;
    },
    progress: { color: "#4B5563", showSpinner: true },
});
