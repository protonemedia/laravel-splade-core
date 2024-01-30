<script setup>
import { inject, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const _spladeBladeHelpers = inject('$spladeBladeHelpers')
const _spladeBridgeState = ref(props.spladeBridge)
const _spladeTemplateBus = inject('$spladeTemplateBus')
const refreshComponent = _spladeBladeHelpers.asyncRefreshComponent(_spladeBridgeState, _spladeTemplateBus)
const spladeRender = {
    inheritAttrs: false,
    name: 'SpladeComponentTimeRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { refreshComponent }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
