import '../css/app.css'
import { createApp } from 'vue/dist/vue.esm-bundler.js'

// for dev
// import { SpladeCorePlugin } from '../../../dist/protone-media-laravel-splade-core'

// for build
import { SpladeCorePlugin } from '@protonemedia/laravel-splade-core'

const app = createApp().use(SpladeCorePlugin, {
    components: import.meta.glob('./splade/*.vue', { eager: true }),
})

app.mount('#app')
