<script setup>
import { BladeComponent } from '@protonemedia/laravel-splade-core'
import { ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const _spladeBridgeState = ref(props.spladeBridge)
const execute = BladeComponent.asyncComponentMethod('execute', _spladeBridgeState)
const sleep = BladeComponent.asyncComponentMethod('sleep', _spladeBridgeState)
const response = ref('-')

const executeWithCallback = () => {
    execute(new Date()).then((data) => {
        response.value = data.data.response
    })
}
const spladeRender = {
    name: 'SpladeComponentBladeMethodRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { executeWithCallback, response, execute, sleep }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId"
        ><template><slot /></template
    ></spladeRender>
</template>
