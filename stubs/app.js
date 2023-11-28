import { createApp } from "vue/dist/vue.esm-bundler.js";
import { SpladeCorePlugin } from "@protonemedia/laravel-splade-core";
import SpladePlugins from "./splade/plugins";

createApp()
    .use(SpladeCorePlugin, {
        plugins: SpladePlugins,
        components: import.meta.glob("./splade/*.vue", { eager: true }),
    })
    .mount("#app");
