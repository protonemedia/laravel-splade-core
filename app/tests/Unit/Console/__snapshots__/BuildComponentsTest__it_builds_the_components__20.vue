<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeTemplateId: String })
const title = ref('Default title')

function updateTitle() {
    title.value = 'New title'
}
const spladeRender = {
    name: 'SpladePropsInTemplateRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeTemplateId: String },
    data: () => ({ title, updateTitle }),
}
</script>
<template>
    <spladeRender :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
