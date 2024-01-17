<script setup>
import { BladeComponent, GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
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
        return { response, execute, fail }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
})
</script>
<template><spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId" /></template>
