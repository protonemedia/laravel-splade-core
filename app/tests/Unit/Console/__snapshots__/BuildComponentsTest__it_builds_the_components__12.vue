<script setup>
import {} from 'vue'
const props = defineProps({
    spladeBridge: Object,
    spladeTemplateId: String,
    mixed: {},
    string: { type: String },
    defaultString: { type: String },
    nullableString: { type: String },
    int: { type: Number },
    bool: { type: Boolean },
    array: { type: Array },
    object: { type: Object },
    nullableInt: { type: Number },
    nullableBool: { type: Boolean },
    nullableArray: { type: Array },
    nullableObject: { type: Object },
    defaultInt: { type: Number },
    defaultBool: { type: Boolean },
    defaultArray: { type: Array },
    multipleTypes: { type: [Array, String, Boolean] },
    renamed: { type: String },
    jsObject: {},
    dataFromMethod: { type: Array },
})
const spladeRender = {
    name: 'SpladeComponentToVuePropRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: {
        spladeBridge: Object,
        spladeTemplateId: String,
        mixed: {},
        string: { type: String },
        defaultString: { type: String },
        nullableString: { type: String },
        int: { type: Number },
        bool: { type: Boolean },
        array: { type: Array },
        object: { type: Object },
        nullableInt: { type: Number },
        nullableBool: { type: Boolean },
        nullableArray: { type: Array },
        nullableObject: { type: Object },
        defaultInt: { type: Number },
        defaultBool: { type: Boolean },
        defaultArray: { type: Array },
        multipleTypes: { type: [Array, String, Boolean] },
        renamed: { type: String },
        jsObject: {},
        dataFromMethod: { type: Array },
    },
    data: () => ({}),
}
</script>
<template>
    <spladeRender
        :splade-bridge="spladeBridge"
        :splade-template-id="spladeTemplateId"
        :mixed="mixed"
        :string="string"
        :default-string="defaultString"
        :nullable-string="nullableString"
        :int="int"
        :bool="bool"
        :array="array"
        :object="object"
        :nullable-int="nullableInt"
        :nullable-bool="nullableBool"
        :nullable-array="nullableArray"
        :nullable-object="nullableObject"
        :default-int="defaultInt"
        :default-bool="defaultBool"
        :default-array="defaultArray"
        :multiple-types="multipleTypes"
        :renamed="renamed"
        :js-object="jsObject"
        :data-from-method="dataFromMethod"
    >
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>
