import type { DefineComponent } from 'vue';

/**
 * Eager page modules, kept out of app/index.ts on purpose: Vite hoists eager glob imports
 * above every other import of their module. Here they load after `vue`, which must be
 * initialised before any pre-bundled PrimeVue/PrimeIcons chunk (vuedraggable's CJS
 * `require('vue')` makes the pre-bundled Vue lazily initialised).
 */
export const pages = import.meta.glob<{ default: DefineComponent }>('../pages/*/index.ts', { eager: true });
