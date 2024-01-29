<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeTemplateId: String })
const message = ref('Hello Included view!')
const spladeRender = {
    name: 'SpladeIncludedViewRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { message }
    },
    props: { spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-template-id="spladeTemplateId"
        ><template><slot /></template
    ></spladeRender>
</template>
