<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeTemplateId: String })
const layoutCounter = ref(0)
const spladeRender = {
    name: 'SpladeSlotRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeTemplateId: String },
    data: () => ({ layoutCounter }),
}
</script>
<template>
    <spladeRender :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
