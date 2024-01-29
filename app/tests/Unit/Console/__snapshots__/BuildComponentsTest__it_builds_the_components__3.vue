<script setup>
import { BladeComponent } from '@protonemedia/laravel-splade-core'
import { ref } from 'vue'
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
const spladeRender = {
    name: 'SpladeComponentBladeMethodCallbacksRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { response, execute, fail }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
