import type { App } from 'vue';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import 'primeicons/primeicons.css';

export function installPrimeVue(app: App): void {
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: '.dark',
            },
        },
        license: 'eyJpZCI6IjJhZTQ0Mjk2LTFlZDctNGQyMS05MzU1LTIyODI4MWNiMTRmYyIsInByb2R1Y3QiOiJwcmltZXVpIiwidGllciI6ImNvbW11bml0eSIsInR5cGUiOiJkZXYiLCJpYXQiOjE3ODgwMjk2MjUsImV4cCI6MTgxOTU2NTYyNX0.X3njUfQsx2VU_7sIOMtWgYXhcTen_VGab59CyAmK5zgzGpoW9-F2QntKLvlWD1m1-TCMTPmuLlKCWYUmzF7mAw'
    });
}
