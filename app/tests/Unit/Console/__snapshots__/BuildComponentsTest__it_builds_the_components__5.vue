<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, onMounted } from 'vue'
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

const spladeRender = h({
    name: 'SpladeComponentDatePickerRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { ...props, emit, setSpladeRef }
    },
})
</script>
<template><spladeRender /></template>
