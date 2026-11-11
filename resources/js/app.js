

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PWA (RNF02): o service worker permite instalar o DoaPlus e receber notificações push
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
}
