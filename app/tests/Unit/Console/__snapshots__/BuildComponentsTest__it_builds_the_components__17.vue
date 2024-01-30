<script setup>
import { ref } from 'vue'
const props = defineProps({
    spladeBridge: Object,
    spladeTemplateId: String,
    title: { type: String, required: false, default: 'Default title' },
})
const subtitle = ref('Default subtitle')
const spladeRender = {
    name: 'SpladeComponentPropsInTemplateRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: {
        spladeBridge: Object,
        spladeTemplateId: String,
        title: { type: String, required: false, default: 'Default title' },
    },
    data: () => ({ subtitle }),
}
</script>
<template>
    <spladeRender :title="title" :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
