<script setup>
import { inject, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const _spladeBladeHelpers = inject('$spladeBladeHelpers')
const _spladeBridgeState = ref(props.spladeBridge)
const _spladeTemplateBus = inject('$spladeTemplateBus')
const refreshComponent = _spladeBladeHelpers.asyncRefreshComponent(_spladeBridgeState, _spladeTemplateBus)
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
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeBridge: Object, spladeTemplateId: String },
    data: () => ({ status, refreshComponent }),
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
