<script setup>
import { BladeComponent, GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, inject, ref } from 'vue'
const props = defineProps(['spladeBridge', 'spladeTemplateId'])
const _spladeBridgeState = ref(props.spladeBridge)
const _spladeTemplateBus = inject('$spladeTemplateBus')
const refreshComponent = BladeComponent.asyncRefreshComponent(_spladeBridgeState, _spladeTemplateBus)

const spladeRender = h({
    inheritAttrs: false,
    name: 'SpladeComponentTimeRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { ...props, refreshComponent }
    },
})
</script>
<template><spladeRender /></template>
