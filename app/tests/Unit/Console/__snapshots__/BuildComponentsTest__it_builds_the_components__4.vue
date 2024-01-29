<script setup>
import { BladeComponent } from '@protonemedia/laravel-splade-core'
import { computed, ref } from 'vue'
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
const spladeRender = {
    name: 'SpladeComponentChangeBladePropRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { message, setMessage }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId"
        ><template><slot /></template
    ></spladeRender>
</template>
