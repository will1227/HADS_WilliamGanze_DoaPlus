// Service worker do DoaPlus (RNF02): página "sem conexão" e notificações push.
// Muda CACHE quando offline.html mudar, para o navegador baixar a versão nova.
const CACHE = 'doaplus-v1';
const OFFLINE = '/offline.html';

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll([OFFLINE, '/icons/icon-192.png'])));
    self.skipWaiting();
});

// Apaga caches de versões antigas
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((nomes) => Promise.all(nomes.filter((nome) => nome !== CACHE).map((nome) => caches.delete(nome))))
            .then(() => self.clients.claim())
    );
});

// Só para abrir páginas: tenta a internet e, sem conexão, mostra o aviso.
// Nada do sistema (agenda, alertas, dados do doador) é guardado no cache.
self.addEventListener('fetch', (event) => {
    if (event.request.mode !== 'navigate') return;
    event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE)));
});

// Chegou um alerta de urgência pelo servidor (AlertaUrgente::toWebPush)
self.addEventListener('push', (event) => {
    const dados = event.data ? event.data.json() : {};

    event.waitUntil(self.registration.showNotification(dados.title || 'DoaPlus', {
        body: dados.body,
        icon: dados.icon,
        badge: dados.badge,
        tag: dados.tag,
        data: dados.data,
    }));
});

// Tocar na notificação abre o painel do doador nos alertas
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(self.clients.openWindow(event.notification.data?.url || '/'));
});
