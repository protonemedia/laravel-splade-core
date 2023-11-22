<script setup>
import { BladeComponent, GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, inject, ref } from 'vue'
const props = defineProps(['spladeBridge', 'spladeTemplateId'])
const _spladeBridgeState = ref(props.spladeBridge)
const _spladeTemplateBus = inject('$spladeTemplateBus')
const refreshComponent = BladeComponent.asyncRefreshComponent(_spladeBridgeState, _spladeTemplateBus)

const status = ref('idle')

refreshComponent.before(() => {
    status.value = 'loading'
})

refreshComponent.then(() => {
    console.log('then refreshed')
})

refreshComponent.finally(() => {
    console.log('finally refreshed')
})

const spladeRender = h({
    name: 'SpladeComponentTimeStateRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { ...props, status, refreshComponent }
    },
})
</script>
<template><spladeRender /></template>
