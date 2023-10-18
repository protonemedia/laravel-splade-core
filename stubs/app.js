import { createApp } from "vue/dist/vue.esm-bundler.js";
import { SpladeCorePlugin } from "@protonemedia/laravel-splade-core";

createApp()
    .use(SpladeCorePlugin, {
        components: import.meta.glob("./splade/*.vue", { eager: true }),
    })
    .mount("#app");
