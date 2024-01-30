<script setup>
import { inject, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const _spladeBladeHelpers = inject('$spladeBladeHelpers')
const _spladeBridgeState = ref(props.spladeBridge)
const execute = _spladeBladeHelpers.asyncComponentMethod('execute', _spladeBridgeState)
const sleep = _spladeBladeHelpers.asyncComponentMethod('sleep', _spladeBridgeState)
const response = ref('-')

const executeWithCallback = () => {
    execute(new Date()).then((data) => {
        response.value = data.data.response
    })
}
const spladeRender = {
    name: 'SpladeComponentBladeMethodRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeBridge: Object, spladeTemplateId: String },
    data: () => ({ executeWithCallback, response, execute, sleep }),
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
