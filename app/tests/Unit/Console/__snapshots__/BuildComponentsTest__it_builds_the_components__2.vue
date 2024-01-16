<script setup>
import { BladeComponent, GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, ref } from 'vue'
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

const spladeRender = h({
    name: 'SpladeComponentBladeMethodRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { executeWithCallback, response, execute, sleep }
    },
    props,
})
</script>
<template><spladeRender /></template>
