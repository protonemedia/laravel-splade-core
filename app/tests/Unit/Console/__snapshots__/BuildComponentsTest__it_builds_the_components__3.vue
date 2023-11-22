<script setup>
import { BladeComponent, GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, ref } from 'vue'
const props = defineProps(['spladeBridge', 'spladeTemplateId'])
const _spladeBridgeState = ref(props.spladeBridge)
const execute = BladeComponent.asyncComponentMethod('execute', _spladeBridgeState)
const fail = BladeComponent.asyncComponentMethod('fail', _spladeBridgeState)

const response = ref('-')

execute.before((data) => {
    response.value = 'waiting...'
})

execute.then((data) => {
    response.value = 'yes!'
})

fail.catch((data) => {
    response.value = 'no!'
})

const spladeRender = h({
    name: 'SpladeComponentBladeMethodCallbacksRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { ...props, response, execute, fail }
    },
})
</script>
<template><spladeRender /></template>
