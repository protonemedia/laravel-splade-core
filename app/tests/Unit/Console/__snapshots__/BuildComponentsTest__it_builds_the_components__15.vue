<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const childVar = ref("What's that, Hawaiian Noises?")
const spladeRender = {
    name: 'SpladeComponentChildRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeBridge: Object, spladeTemplateId: String },
    data: () => ({ childVar }),
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
