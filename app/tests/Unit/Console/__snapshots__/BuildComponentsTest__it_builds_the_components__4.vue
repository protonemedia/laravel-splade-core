<script setup>
import { computed, inject, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const _spladeBladeHelpers = inject('$spladeBladeHelpers')
const _spladeBridgeState = ref(props.spladeBridge)
const setMessage = _spladeBladeHelpers.asyncComponentMethod('setMessage', _spladeBridgeState)
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
    props: { spladeBridge: Object, spladeTemplateId: String },
    data: () => ({ message, setMessage }),
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
