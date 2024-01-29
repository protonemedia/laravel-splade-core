<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeTemplateId: String })
const message = ref('Hello World!')
const spladeRender = {
    name: 'SpladeRegularViewRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { message }
    },
    props: { spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
