<script setup>
import { onMounted } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String, modelValue: {} })
const $refs = {}
const setSpladeRef = (key, value) => ($refs[key] = value)
import flatpickr from 'flatpickr'

const emit = defineEmits(['update:modelValue'])

onMounted(() => {
    let instance = flatpickr($refs.date, {
        onChange: (selectedDates, newValue) => {
            emit('update:modelValue', newValue)
        },
    })

    instance.setDate(props.modelValue)
})
const spladeRender = {
    name: 'SpladeComponentDatePickerRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { emit, setSpladeRef }
    },
    props: { spladeBridge: Object, spladeTemplateId: String, modelValue: {} },
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId" :model-value="modelValue"
        ><template><slot /></template
    ></spladeRender>
</template>
