import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            content: [
                "./vendor/diogogpinto/filament-auth-ui-enhancer/resources/**/*.blade.php",
            ],
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/custom-login.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
