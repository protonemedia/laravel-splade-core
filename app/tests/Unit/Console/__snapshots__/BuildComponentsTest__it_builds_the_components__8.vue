<script setup>
import { BladeComponent, GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { inject, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
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
const spladeRender = {
    name: 'SpladeComponentTimeStateRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { status, refreshComponent }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template><spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId" /></template>
