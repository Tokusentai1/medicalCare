import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    //to let the css work with ngrok
    //server: {
    //    host: "0.0.0.0", // make it accessible from external
    //    port: 5173,
    //    hmr: {
    //        host: "696a-5-163-254-213.ngrok-free.app",
    //        protocol: "wss", // required for HTTPS/ngrok
    //  },
    //},
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
