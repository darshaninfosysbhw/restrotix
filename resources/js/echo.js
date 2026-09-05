import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

const isHttps = window.location.protocol === "https:";

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: isHttps ? 443 : 8080,
    wssPort: isHttps ? 443 : 8080,
    forceTLS: isHttps,
    enabledTransports: ["ws", "wss"],
});
