<script setup>
import { BladeComponent, GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { computed, h, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const _spladeBridgeState = ref(props.spladeBridge)
const setMessage = BladeComponent.asyncComponentMethod('setMessage', _spladeBridgeState)
const message = computed({
    get() {
        return _spladeBridgeState.value.data.message
    },
    set(newValue) {
        _spladeBridgeState.value.data.message = newValue
    },
})
const spladeRender = h({
    name: 'SpladeComponentChangeBladePropRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { message, setMessage }
    },
    props,
})
</script>
<template><spladeRender /></template>
