import GenericSpladeComponent from './components/GenericSpladeComponent.vue'

class EventBus {
    constructor(id) {
        this.id = id
        this.events = {}
    }

    on(event, callback) {
        if (!this.events[event]) {
            this.events[event] = []
        }

        this.events[event].push(callback)
    }

    off(event, callback) {
        if (!this.events[event]) {
            return
        }

        this.events[event] = this.events[event].filter((cb) => cb !== callback)
    }

    emit(event, data) {
        if (!this.events[event]) {
            return
        }

        this.events[event].forEach((callback) => {
            callback(data)
        })
    }
}

export default {
    install: (app, options) => {
        app.provide('$spladeTemplateBus', new EventBus('splade-templates'))
        app.component('GenericSpladeComponent', GenericSpladeComponent)

        Object.entries(options.components).forEach(([path, m]) => {
            const componentName = path
                .split('/')
                .pop()
                .replace(/\.\w+$/, '')

            app.component(componentName, m.default)
        })
    },
}
