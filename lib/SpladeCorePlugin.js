import GenericSpladeComponent from "./GenericSpladeComponent.vue";

class EventBus {
    constructor(id) {
        this.id = id;
        this.events = {};
    }

    on(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }

        this.events[event].push(callback);
    }

    off(event, callback) {
        if (!this.events[event]) {
            return;
        }

        this.events[event] = this.events[event].filter((cb) => cb !== callback);
    }

    emit(event, data) {
        if (!this.events[event]) {
            return;
        }

        this.events[event].forEach((callback) => {
            callback(data);
        });
    }
}

export default {
    install: (app, options) => {
        app.provide("$spladeTemplateBus", new EventBus("splade-templates"));
        app.component("GenericSpladeComponent", GenericSpladeComponent);

        options = options || {};
        options.components = options.components || {};

        if (options.suppress_compile_errors !== false) {
            app.config.compilerOptions.onError = (error) => {
                import("./CompilerErrorMessages.js").then(
                    (CompilerErrorMessages) => {
                        console.error({
                            message:
                                CompilerErrorMessages.default[error.code] ||
                                "Unknown compiler error",
                            lineNumber: error.lineNumber,
                            compileError: error,
                        });
                    },
                );
            };
        }

        // options.plugins
        if (options.plugins) {
            options.plugins(app);
        }

        // options.components
        for (const [path, m] of Object.entries(options.components)) {
            const componentName = path
                .split("/")
                .pop()
                .replace(/\.\w+$/, "");

            app.component(componentName, m.default);
        }
    },
};
