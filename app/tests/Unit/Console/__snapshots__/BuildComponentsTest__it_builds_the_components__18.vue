<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const rootCounter = ref(0)

const emit = defineEmits(['incremented'])

function increment() {
    rootCounter.value++
    emit('incremented')
}
const spladeRender = {
    name: 'SpladeComponentRootRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeBridge: Object, spladeTemplateId: String },
    data: () => ({ emit, increment, rootCounter }),
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
