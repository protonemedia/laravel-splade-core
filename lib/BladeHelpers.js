import { default as Axios } from "axios";
import { ref, nextTick } from "vue";
import set from "lodash-es/set";

function asyncComponentMethod(method, componentStateRef) {
    const loading = ref(false);

    const callbacks = {
        before: [],
        then: [],
        catch: [],
        finally: [],
    };

    const execute = async (...data) => {
        loading.value = true;

        callbacks.before.forEach((callback) => {
            callback(data);
        });

        const promise = executeComponentMethod(
            method,
            componentStateRef,
            ...data,
        );

        promise.then((response) => {
            callbacks.then.forEach((callback) => {
                callback(response, data);
            });
        });

        promise.catch((e) => {
            callbacks.catch.forEach((callback) => {
                callback(e, data);
            });
        });

        promise.finally(() => {
            callbacks.finally.forEach((callback) => {
                callback(data);
            });

            loading.value = false;
        });

        return promise;
    };

    execute.loading = loading;

    execute.before = (callback) => callbacks.before.push(callback);
    execute.then = (callback) => callbacks.then.push(callback);
    execute.catch = (callback) => callbacks.catch.push(callback);
    execute.finally = (callback) => callbacks.finally.push(callback);

    return execute;
}

async function executeComponentMethod(method, componentStateRef, ...data) {
    const spladeBridge = componentStateRef.value;

    const promise = Axios.post(
        spladeBridge.invoke_url,
        {
            instance: spladeBridge.instance,
            signature: spladeBridge.signature,
            original_url: spladeBridge.original_url,
            original_verb: spladeBridge.original_verb,
            template_hash: spladeBridge.template_hash,
            props: spladeBridge.data,
            method,
            data,
        },
        {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-Splade-Request-Hash": uuidv4(),
                Accept: "text/html, application/xhtml+xml",
            },
        },
    );

    promise.then((response) => {
        componentStateRef.value.instance = response.data.instance;
        componentStateRef.value.signature = response.data.signature;
        componentStateRef.value.data;

        for (const [key, value] of Object.entries(response.data.data)) {
            set(componentStateRef.value.data, key, value);
        }
    });

    return promise;
}

// https://stackoverflow.com/a/2117523
function uuidv4() {
    return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, (c) =>
        (
            c ^
            (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
        ).toString(16),
    );
}

function refreshComponent(componentStateRef, spladeTemplateBus) {
    const spladeBridge = componentStateRef.value;

    const promise = Axios.get(spladeBridge.original_url, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-Splade-Component-Refresh": `${spladeBridge.template_hash}`,
            "X-Splade-Request-Hash": uuidv4(),
            Accept: "text/html, application/xhtml+xml",
        },
    });

    promise.then(async (response) => {
        for (const [key, value] of Object.entries(response.data.templates)) {
            await nextTick(() =>
                spladeTemplateBus.emit(`template:${key}`, {
                    template: value,
                    hash: response.headers["x-splade-request-hash"],
                }),
            );
        }
    });

    return promise;
}

function asyncRefreshComponent(componentStateRef, spladeTemplateBus) {
    const loading = ref(false);

    const callbacks = {
        before: [],
        then: [],
        catch: [],
        finally: [],
    };

    const execute = async () => {
        callbacks.before.forEach((callback) => {
            callback();
        });

        loading.value = true;

        const promise = refreshComponent(componentStateRef, spladeTemplateBus);

        promise.then((response) => {
            callbacks.then.forEach((callback) => {
                callback(response);
            });
        });

        promise.catch((e) => {
            callbacks.catch.forEach((callback) => {
                callback(e);
            });
        });

        promise.finally(() => {
            callbacks.finally.forEach((callback) => {
                callback();
            });

            loading.value = false;
        });

        return promise;
    };

    execute.loading = loading;

    execute.before = (callback) => callbacks.before.push(callback);
    execute.then = (callback) => callbacks.then.push(callback);
    execute.catch = (callback) => callbacks.catch.push(callback);
    execute.finally = (callback) => callbacks.finally.push(callback);

    return execute;
}

export default {
    asyncComponentMethod,
    executeComponentMethod,
    refreshComponent,
    asyncRefreshComponent,
};
