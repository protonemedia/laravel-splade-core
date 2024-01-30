<script setup>
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const emit = defineEmits(['trigger'])

const trigger = () => {
    emit('trigger')
}
const spladeRender = {
    name: 'SpladeComponentEmitRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeBridge: Object, spladeTemplateId: String },
    data: () => ({ emit, trigger }),
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
