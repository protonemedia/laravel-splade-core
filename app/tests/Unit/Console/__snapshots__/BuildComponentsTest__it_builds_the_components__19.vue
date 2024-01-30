<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeTemplateId: String })
const show = ref(false)

function toggle() {
    show.value = !show.value
}
const spladeRender = {
    name: 'SpladeEmitRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeTemplateId: String },
    data: () => ({ show, toggle }),
}
</script>
<template>
    <spladeRender :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
