<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeTemplateId: String })
const message = ref('Hello Included view!')
const spladeRender = {
    name: 'SpladeIncludedViewRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeTemplateId: String },
    data: () => ({ message }),
}
</script>
<template>
    <spladeRender :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
