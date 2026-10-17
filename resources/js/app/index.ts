import '@/shared/api/http';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { installPrimeVue } from './providers/primevue';
import { pages } from './pages';

const appName = import.meta.env.VITE_APP_NAME || 'Project OS';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const page = pages[`../pages/${name}/index.ts`];

        if (!page) {
            throw new Error(`Page not found: ${name}`);
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        installPrimeVue(app);

        app.mount(el);
    },
    progress: {
        color: '#4457C2',
    },
});
